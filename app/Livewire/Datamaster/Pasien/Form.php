<?php

namespace App\Livewire\Datamaster\Pasien;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Pasien;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data;
    public $nama, $ihs, $nik, $rm, $jenis_kelamin, $birth_place, $tanggal_lahir, $tanggal_daftar, $alamat, $no_hp;

    public function submit()
    {
        $this->validateWithCustomMessages([
            'nama' => 'required',
            'nik' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->ihs = $this->ihs;
            $this->data->nik = $this->nik;
            $this->data->alamat = $this->alamat;
            $this->data->jenis_kelamin = $this->jenis_kelamin;
            $this->data->tanggal_lahir = $this->tanggal_lahir;
            $this->data->no_hp = $this->no_hp;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('datamaster/pasien');
    }

    public function mount(Pasien $data)
    {
        
        $this->data = $data;
        $this->rm = $this->data->id;
        $this->fill($this->data->toArray());
        $this->tanggal_lahir = $this->data->tanggal_lahir->format('Y-m-d');
    }
    
    public function render()
    {
        return view('livewire.datamaster.pasien.form');
    }
}
