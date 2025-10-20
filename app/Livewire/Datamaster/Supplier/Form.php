<?php

namespace App\Livewire\Datamaster\Supplier;

use Livewire\Component;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $previous;
    public $nama, $deskripsi, $alamat, $no_hp, $konsinyator = false;

    public function submit()
    {
        $this->validateWithCustomMessages([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->alamat = $this->alamat;
            $this->data->no_hp = $this->no_hp;
            $this->data->konsinyator = $this->konsinyator ? 1 : 0;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(Supplier $data)
    {
        $this->previous = url()->previous();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->konsinyator = $this->data->konsinyator == 1 ? true : false;
    }

    public function render()
    {
        return view('livewire.datamaster.supplier.form');
    }
}
