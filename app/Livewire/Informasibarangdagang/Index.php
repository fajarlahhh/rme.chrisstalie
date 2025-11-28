<?php

namespace App\Livewire\Informasibarangdagang;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Barang;

class Index extends Component
{
    #[Url]
    public $barangId;

    public $dataBarang, $barang;

    public function updatedBarangId($id)
    {
        $this->dataBarang = Barang::find($id);
    }
    public function mount()
    {
        if ($this->barangId) {
            $this->dataBarang = Barang::find($this->barangId);
        } else {
            $this->dataBarang = null;
        }
    }


    public function render()
    {
        return view('livewire.informasibarangdagang.index');
    }
}
