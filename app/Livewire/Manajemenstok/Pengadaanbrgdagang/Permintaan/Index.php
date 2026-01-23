<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Permintaan;

use Livewire\Component;
use App\Models\PermintaanPengadaan;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $status = 'Pending', $tanggal;

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function delete($id)
    {
        try {
            PermintaanPengadaan::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.permintaan.index', [
            'data' => PermintaanPengadaan::with([
                'pengguna.pegawai',
                'permintaanPengadaanDetail.barangSatuan.satuanKonversi',
                'permintaanPengadaanDetail.barangSatuan.barang',
                'pemesananPengadaan.stokMasuk',
                'VerifikasiPengadaanPending',
                'VerifikasiPengadaanDisetujui',
                'VerifikasiPengadaanDitolak',
                'verifikasiPengadaan.pengguna'
            ])
                ->where(fn($q) => $q
                    ->where('deskripsi', 'like', '%' . $this->cari . '%'))
                ->when($this->status == 'Pending', fn($q) => $q->whereHas('verifikasiPengadaan', function ($q) {
                    $q->whereNull('status');
                })->orWhereDoesntHave('verifikasiPengadaan'))
                ->when($this->status == 'Ditolak', fn($q) => $q->whereHas('VerifikasiPengadaanDitolak'))
                ->when($this->status == 'Disetujui', fn($q) => $q->whereHas('VerifikasiPengadaanDisetujui')->where('created_at', 'like', $this->tanggal . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
