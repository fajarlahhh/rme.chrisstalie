<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Permintaan;

use Livewire\Component;
use App\Models\PermintaanPembelian;
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
            PermintaanPembelian::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.permintaan.index', [
            'data' => PermintaanPembelian::with([
                'pengguna.pegawai',
                'permintaanPembelianDetail.barangSatuan.satuanKonversi',
                'permintaanPembelianDetail.barangSatuan.barang',
                'pembelian.stokMasuk',
                'verifikasiPending',
                'verifikasiDisetujui',
                'verifikasiDitolak',
                'verifikasi.pengguna'
            ])
                ->where(fn($q) => $q
                    ->where('deskripsi', 'like', '%' . $this->cari . '%'))
                ->when($this->status == 'Pending', fn($q) => $q->whereHas('verifikasi', function ($q) {
                    $q->whereNull('status');
                })->orWhereDoesntHave('verifikasi'))
                ->when($this->status == 'Ditolak', fn($q) => $q->whereHas('verifikasiDitolak'))
                ->when($this->status == 'Disetujui', fn($q) => $q->whereHas('verifikasiDisetujui')->where('created_at', 'like', $this->tanggal . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
