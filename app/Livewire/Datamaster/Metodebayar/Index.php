<?php

namespace App\Livewire\Datamaster\Metodebayar;

use Livewire\Component;
use App\Models\MetodeBayar;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari;

    public function delete($id)
    {
        try {
            $data = MetodeBayar::findOrFail($id);
            $data->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('error', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.datamaster.metodebayar.index', [
            'data' => MetodeBayar::where('nama', 'like', '%' . $this->cari . '%')->paginate(10)
        ]);
    }
}
