<?php

namespace App\Livewire\Pengaturan\Nakes;

use App\Models\Nakes;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $aktif = 1;


    public function delete($id)
    {
        Nakes::findOrFail($id)->delete();
    }

    public function permanentdelete($id)
    {
        Nakes::findOrFail($id)->forceDelete();
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function restore($id)
    {
        Nakes::withTrashed()->findOrFail($id)->restore();
    }

    public function render()
    {
        return view('livewire.pengaturan.nakes.index', [
            'data' => Nakes::where(
                fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('kepegawaianPegawai', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
            )->where('aktif', $this->aktif)
                ->with('pengguna.kepegawaianPegawai', 'kepegawaianPegawai')
                ->orderBy('nama')->paginate(10)
        ]);
    }
}
