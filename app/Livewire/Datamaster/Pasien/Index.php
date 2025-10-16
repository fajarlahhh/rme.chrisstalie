<?php

namespace App\Livewire\Datamaster\Pasien;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Pasien;

class Index extends Component
{
    use WithPagination;

    #[Url] 
    public $cari, $exist = 1;


    public function render()
    {
        return view('livewire.datamaster.pasien.index', [
            'data' => Pasien::with('pengguna')->where(fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%')->orWhere('id', 'like', '%' . $this->cari . '%'))
                ->orderBy('id')->paginate(10)
        ]);
    }
}
