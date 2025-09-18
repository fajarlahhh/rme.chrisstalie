<?php

namespace App\Livewire\Klinik\Sitemarking;

use Livewire\Component;
use App\Models\Registrasi;

class Index extends Component
{

    public $dataRegistrasi = [], $data;
    public $registrasi_id;

    public function mount(Registrasi $data)
    {
        if ($data) {
            $this->data = $data;
        } else {
            $this->data = null;
        }
        $this->dataRegistrasi = Registrasi::whereHas('pemeriksaanAwal')
            ->whereDoesntHave('siteMarking')
            ->whereDoesntHave('pembayaran')
            ->get();
    }

    public function updatedRegistrasiId($id)
    {
        $this->data = Registrasi::find($id);
    }

    public function render()
    {
        return view('livewire.klinik.sitemarking.index');
    }
}
