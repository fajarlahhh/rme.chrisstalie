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
    public $cari, $status = 'Belum Buat SP', $bulan;

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
            PengadaanPemesanan::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        });
    }
    private function getData()
    {
        if ($this->status == 'Belum Buat SP') {
            $data = PengadaanPermintaan::with([
                'pengguna',
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
                'penanggungJawab',
                'pengguna',
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
                ->where('tanggal', 'like', $this->bulan . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return $data;
        }
    }

    public function render()
    {
        // $permintaan = DB::table('pengadaan_permintaan')
        //     ->whereNotIn('id', function ($query) {
        //         $query->select('pengadaan_permintaan_id')
        //             ->from('pengadaan_pemesanan');
        //     })
        //     ->whereIn('id', function ($query) {
        //         $query->select('pengadaan_permintaan_id')
        //             ->from('stok_masuk');
        //     })
        //     ->get();
        // dd($permintaan);
        return view('livewire.manajemenstok.pengadaanbrgdagang.pemesanan.index', [
            'data' => $this->getData()
        ]);
    }
}
