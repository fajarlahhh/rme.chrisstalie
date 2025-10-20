<?php

namespace App\Livewire\Datamaster\Kodeakun;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\KodeAkun;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari;

    public function delete($id)
    {
        try {
            KodeAkun::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.datamaster.kodeakun.index', [
            'data' => KodeAkun::where(fn($q) => $q
                ->where('id', 'like', '%' . $this->cari . '%')
                ->orWhere('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('id')
                ->paginate(10)
        ]);
    }
}
