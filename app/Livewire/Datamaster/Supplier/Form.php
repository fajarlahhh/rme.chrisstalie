<?php

namespace App\Livewire\Datamaster\Supplier;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data;
    public $nama, $deskripsi, $alamat, $no_hp, $konsinyator = false, $kode_akun_id;
    public $dataKodeAkun = [];
    public function submit()
    {
        $this->validateWithCustomMessages([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'kode_akun_id' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->alamat = $this->alamat;
            $this->data->no_hp = $this->no_hp;
            // $this->data->konsinyator = $this->konsinyator ? 1 : 0;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->deskripsi = $this->deskripsi;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/datamaster/supplier');
    }

    public function mount(Supplier $data)
    {

        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->dataKodeAkun = KodeAkun::detail()->where('kategori', 'Kewajiban')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.datamaster.supplier.form');
    }
}
