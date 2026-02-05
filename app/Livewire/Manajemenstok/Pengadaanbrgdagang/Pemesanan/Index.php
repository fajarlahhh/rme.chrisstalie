<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Pemesanan;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PengadaanPemesanan;
use Illuminate\Support\Facades\DB;
use App\Models\PengadaanPermintaan;
use App\Models\PengadaanVerifikasi;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $status = 'Belum Dipesan', $bulan;

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function print($id)
    {
        $data = PengadaanPemesanan::findOrFail($id);
        $cetak = view('livewire.manajemenstok.pengadaanbrgdagang.pemesanan.cetak', [
            'data' => $data,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            PengadaanVerifikasi::where('pengadaan_pemesanan_id', $id)
                ->where('jenis', 'Persetujuan Pemesanan Pengadaan')
                ->forceDelete();
            PengadaanPemesanan::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        });
    }
    private function getData()
    {
        if ($this->status == 'Belum Dipesan') {
            $data = PengadaanPermintaan::with([
                'pengguna.kepegawaianPegawai',
                'pengadaanPermintaanDetail.barangSatuan.satuanKonversi',
                'pengadaanPermintaanDetail.barangSatuan.barang',
                'pengadaanPemesanan.stokMasuk',
                'pengadaanPemesananDetail'
            ])
                ->whereRaw('(select ifnull(sum(qty),0) from pengadaan_pemesanan_detail where pengadaan_permintaan_id = pengadaan_permintaan.id ) < (select sum(qty_disetujui) from pengadaan_permintaan_detail where pengadaan_permintaan_id = pengadaan_permintaan.id)')
                ->where(
                    fn($q) => $q
                        ->where('deskripsi', 'like', '%' . $this->cari . '%')
                        ->when(auth()->user()->hasRole('operator|guest'), fn($q) => $q->whereIn('jenis_barang', ['Persediaan Apotek', 'Alat Dan Bahan']))
                )
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return $data;
        } else {
            $data = PengadaanPemesanan::with([
                'supplier',
                'pengguna.kepegawaianPegawai',
                'pengadaanPemesananDetail.barangSatuan.barang',
                'pengadaanPemesananDetail.barangSatuan.satuanKonversi',
                'pengadaanPermintaan',
                'pengadaanPemesananVerifikasi.pengguna',
                'stokMasuk',
            ])
                ->where(fn($z) => $z->whereHas('pengadaanPermintaan', function ($q) {
                    $q->where(
                        fn($q) => $q
                            ->where('deskripsi', 'like', '%' . $this->cari . '%')
                            ->when(auth()->user()->hasRole('operator|guest'), fn($q) => $q->whereIn('jenis_barang', ['Persediaan Apotek', 'Alat Dan Bahan']))
                    );
                })->orWhereHas('supplier', function ($q) {
                    $q->where('nama', 'like', '%' . $this->cari . '%');
                })
                    ->orWhere('catatan', 'like', '%' . $this->cari . '%'))
                ->when($this->status == 'Pending Persetujuan', fn($q) => $q->whereHas('pengadaanPemesananVerifikasi', fn($q) => $q->whereNull('status')))
                ->when($this->status == 'Sudah Persetujuan', fn($q) => $q->whereHas('pengadaanPemesananVerifikasi', fn($q) => $q->where('status', 'Disetujui')->where('waktu_verifikasi', 'like', $this->bulan . '%')))
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return $data;
        }
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.pemesanan.index', [
            'data' => $this->getData()
        ]);
    }
}
