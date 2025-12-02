<?php

namespace App\Livewire\Informasi\Tariftindakan;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\TarifTindakan;

class Index extends Component
{
    #[Url]
    public $tarifTindakanId;

    public $dataTarifTindakan, $tarifTindakan;

    public function updatedTarifTindakanId($id)
    {
        $this->dataTarifTindakan = TarifTindakan::find($id);
    }
    public function mount()
    {
        if ($this->tarifTindakanId) {
            $this->dataTarifTindakan = TarifTindakan::find($this->tarifTindakanId);
        } else {
            $this->dataTarifTindakan = null;
        }
    }

    public function render()
    {
        return view('livewire.informasi.tariftindakan.index');
    }
}
