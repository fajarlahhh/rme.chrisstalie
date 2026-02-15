<?php

namespace App\Livewire\Pengaturan\Metodebayar;

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
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.pengaturan.metodebayar.index', [
            'data' => MetodeBayar::with(['pengguna','kodeAkun'])->where('nama', 'like', '%' . $this->cari . '%')->orderBy('nama', 'asc')->paginate(10)
        ]);
    }
}
