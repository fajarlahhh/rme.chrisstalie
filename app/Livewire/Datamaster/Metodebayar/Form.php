<?php

namespace App\Livewire\Datamaster\Metodebayar;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\MetodeBayar;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $previous, $dataKodeAkun = [];
    public $nama, $kode_akun_id;

    public function submit()
    {
        $this->validateWithCustomMessages([
            'nama' => 'required',
            'kode_akun_id' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(MetodeBayar $data)
    {
        $this->previous = url()->previous();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->dataKodeAkun = KodeAkun::detail()->where('id', 'like', '111%')->where('kategori', 'Aktiva')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.datamaster.metodebayar.form');
    }
}
