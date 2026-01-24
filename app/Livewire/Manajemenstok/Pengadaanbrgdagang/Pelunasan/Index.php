<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Pelunasan;

use Livewire\Component;
use App\Models\PelunasanPengadaan;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $bulan;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        PelunasanPengadaan::findOrFail($id)->forceDelete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view(
            'livewire.manajemenstok.pengadaanbrgdagang.pelunasan.index',
            [
                'data' => PelunasanPengadaan::with(['pemesananPengadaan', 'jurnalKeuangan', 'pengguna.pegawai', 'kodeAkunPembayaran'])->where('created_at', 'like', $this->bulan . '%')
                    ->orderBy('created_at', 'desc')->paginate(10)
            ]
        );
    }
}
