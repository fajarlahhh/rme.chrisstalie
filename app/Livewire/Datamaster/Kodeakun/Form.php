<?php

namespace App\Livewire\Datamaster\Kodeakun;

use App\Models\KodeAkun;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data;
    
    public $kode;
    public $nama;
    public $kategori;
    public $parent_id;
    public $dataParent = [];

    public function updatedKategori($value)
    {
        $this->dataParent = KodeAkun::where('kategori', $value)->where('detial', '0')->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'kode' => 'required|unique:kode_akun,id,' . $this->data->id,
            'nama' => 'required',
            'kategori' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->id = $this->kode;
            $this->data->nama = $this->nama;
            $this->data->kategori = $this->kategori;
            $this->data->parent_id = $this->parent_id;
            $this->data->detail = 1;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            if ($this->data->parent_id) {
                KodeAkun::where('id', $this->data->parent_id)->update([
                    'detail' => 1
                ]);
            }

            if ($this->parent_id) {
                KodeAkun::where('id', $this->parent_id)->update([
                    'detail' => 0
                ]);
            }

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/datamaster/kodeakun');
    }

    public function mount(KodeAkun $data)
    {
        
        $this->data = $data;
        $this->kode = $this->data->id;
        $this->fill($this->data->toArray());
    }

    public function render()
    {
        return view('livewire.datamaster.kodeakun.form');
    }
}
