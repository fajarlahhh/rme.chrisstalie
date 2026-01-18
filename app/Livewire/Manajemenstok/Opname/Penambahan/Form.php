<?php

namespace App\Livewire\Manajemenstok\Opname\Penambahan;

use Livewire\Component;
use App\Traits\CustomValidationTrait;
use App\Class\BarangClass;
class Form extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [], $barang, $dataStok = [], $barang_id, $catatan, $qty_masuk;

    public function updatedBarangId($value)
    {
        $this->barang = collect($this->dataBarang)->firstWhere('id', $value);
    }

    public function mount()
    {
        $this->barang_id = '';
        $this->dataBarang = BarangClass::getBarang();
    }

    public function render()
    {
        return view('livewire.manajemenstok.opname.penambahan.form');
    }
}
