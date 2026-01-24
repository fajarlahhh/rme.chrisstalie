<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Pembelian;

use Livewire\Component;
use App\Models\PemesananPengadaan;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PermintaanPengadaan;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $bulan, $status = 1;

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        PemesananPengadaan::findOrFail($id)->forceDelete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.pembelian.index', [
            'data' => $this->status == 1 ? PermintaanPengadaan::with([
                'pengguna.pegawai',
                'permintaanPengadaanDetail.barangSatuan.satuanKonversi',
                'permintaanPengadaanDetail.barangSatuan.barang',
                'verifikasiPengadaan.pengguna.pegawai' => fn($q) => $q->whereNotNull('status')
            ])
                ->when($this->status == 'Pending', fn($q) => $q->whereHas('verifikasiPengadaan', function ($q) {
                    $q->whereNull('status');
                }))
                ->whereHas('verifikasiPengadaan', function ($q) {
                    $q->whereNotNull('status');
                })
                ->whereDoesntHave('pemesananPengadaan')
                ->where(fn($q) => $q
                    ->where('deskripsi', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10) :
                PemesananPengadaan::with(['pemesananPengadaanDetail.barangSatuan.barang', 'pengguna.pegawai', 'stokMasuk', 'pelunasanPemesananPengadaan', 'supplier', 'permintaanPengadaan'])
                ->where('jenis', 'Barang Dagang')
                ->where('tanggal', 'like', $this->bulan . '%')
                ->where(fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
