<?php

namespace App\Livewire\Datamaster\Asetinventaris;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Aset;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $kategori;

    public function render()
    {
        return view('livewire.datamaster.asetinventaris.index', [
            'data' => Aset::with([
                'pengguna'
            ])
                ->where('kategori', $this->kategori)
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
