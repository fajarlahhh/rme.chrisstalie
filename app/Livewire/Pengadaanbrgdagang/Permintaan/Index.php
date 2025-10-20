<?php

namespace App\Livewire\Pengadaanbrgdagang\Permintaan;

use Livewire\Component;
use App\Models\PermintaanPembelian;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $status = 'Pending';

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
        return view('livewire.pengadaanbrgdagang.permintaan.index', [
            'data' => PermintaanPembelian::with([
                'pengguna',
                'permintaanPembelianDetail',
                'verifikasiPending',
                'verifikasiDisetujui',
                'verifikasiDitolak'
            ])
                ->where(fn($q) => $q
                    ->where('deskripsi', 'like', '%' . $this->cari . '%'))
                ->when($this->status == 'Pending', fn($q) => $q->whereHas('verifikasi', function ($q) {
                    $q->whereNull('status');
                })->orWhereDoesntHave('verifikasi'))
                ->when($this->status == 'Disetujui', fn($q) => $q->whereHas('verifikasiDisetujui'))
                ->when($this->status == 'Ditolak', fn($q) => $q->whereHas('verifikasiDitolak'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
