<?php

namespace App\Livewire\Pengaturan\Shift;

use App\Models\Shift;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    
    public $data;
    public $nama;
    public $jam_masuk;
    public $jam_pulang;

    public function submit()
    {
        $this->validateWithCustomMessages([
            'nama' => 'required',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->jam_masuk = $this->jam_masuk;
            $this->data->jam_pulang = $this->jam_pulang;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();
        });
        $this->redirect('/pengaturan/shift');
    }

    public function mount(Shift $data)
    {

        $this->data = $data;
        $this->fill($this->data->toArray());
    }

    public function render()
    {
        return view('livewire.pengaturan.shift.form');
    }
}
