<?php

namespace App\Livewire\Pengadaan\Verifikasi;

use Livewire\Component;
use App\Models\Verifikasi;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PermintaanPembelian;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $status = 'Pending';

    public function delete($id)
    {
        try {
            Verifikasi::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('error', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.pengadaan.verifikasi.index', [
            'data' => PermintaanPembelian::with([
                'pengguna',
                'permintaanPembelianDetail',
            ])->with(['verifikasi' => fn($q) => $q->whereNotNull('status')])
                ->when($this->status == 'Pending', fn($q) => $q->whereHas('verifikasi', function ($q) {
                    $q->whereNull('status');
                }))
                ->when($this->status == 'Terverifikasi', fn($q) => $q->whereHas('verifikasi', function ($q) {
                    $q->whereNotNull('status');
                }))
                ->where(fn($q) => $q
                    ->where('deskripsi', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
