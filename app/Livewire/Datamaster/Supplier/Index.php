<?php

namespace App\Livewire\Datamaster\Supplier;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $exist = 1;

    public function updated()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Supplier::findOrFail($id)
            ->delete();
    }

    public function render()
    {
        return view('livewire.datamaster.supplier.index', [
            'data' => Supplier::where(fn($q) => $q
                ->where('nama', 'like', '%' . $this->cari . '%'))
                ->when($this->exist == '2', fn($q) => $q->onlyTrashed())
                ->with('pengguna.kepegawaianPegawai')
                ->with('kodeAkun')
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
