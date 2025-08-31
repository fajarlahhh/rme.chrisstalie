<?php

namespace App\Livewire\Hakakses;

use App\Models\Pengguna;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $exist = 1;


    public function delete($id)
    {
        if ($this->key != 1) {
            Pengguna::findOrFail($id)
                ->delete();
            $this->reset(['key']);
        }
    }

    public function permanentDelete($id)
    {
        if ($this->key != 1) {
            Pengguna::findOrFail($id)
                ->forceDelete();
            $this->reset(['key']);
        }
    }

    public function restore($id)
    {
        Pengguna::withTrashed()
            ->findOrFail($id)
            ->restore();
    }

    public function render()
    {
        return view('livewire.hakakses.index', [
            'data' => Pengguna::where('uid', '!=', 'rafaskinclinic@gmail.com')
                ->where(
                    fn($q) => $q
                        ->where('uid', 'like', '%' . $this->cari . '%')
                        ->orWhere('nama', 'like', '%' . $this->cari . '%')
                )
                ->when($this->exist == '2', fn($q) => $q->onlyTrashed())
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
