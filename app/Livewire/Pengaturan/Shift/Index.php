<?php

namespace App\Livewire\Pengaturan\Shift;

use App\Models\Shift;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari;

    public function delete($id)
    {
        try {
            Shift::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pengaturan.shift.index', [
            'data' => Shift::where('nama', 'like', '%' . $this->cari . '%')->with('pengguna')->orderBy('nama')->paginate(10)
        ]);
    }
}
