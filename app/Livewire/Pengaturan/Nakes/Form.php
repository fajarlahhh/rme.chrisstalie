<?php

namespace App\Livewire\Pengaturan\Nakes;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Pegawai;
use App\Models\Nakes;

class Form extends Component
{
    public $data, $previous, $dataPegawai = [], $pegawai;
    public $nama, $ihs, $nik, $alamat, $no_hp, $dokter = false, $pegawai_id;

    public function updatedPegawaiId($value)
    {
        $this->reset('nama', 'ihs', 'nik', 'alamat', 'no_hp', 'dokter');
        if ($value) {
            $this->pegawai = Pegawai::find($this->pegawai_id);
            $this->nama = $this->pegawai->nama;
            $this->ihs = $this->pegawai->ihs;
            $this->nik = $this->pegawai->nik;
            $this->alamat = $this->pegawai->alamat;
            $this->no_hp = $this->pegawai->no_hp;
        } else {
            $this->pegawai = null;
        }
    }

    public function submit()
    {
        if (!$this->pegawai_id) {
            $this->validate([
                'nama' => 'required',
                'nik' => 'required',
                'alamat' => 'required',
                'no_hp' => 'required',
            ]);
        }

        DB::transaction(function () {
            $this->data->pegawai_id = $this->pegawai_id;
            $this->data->ihs = $this->ihs;
            if (!$this->pegawai_id) {
                $this->data->nama = $this->nama;
                $this->data->nik = $this->nik;
                $this->data->alamat = $this->alamat;
                $this->data->no_hp = $this->no_hp;
            }
            $this->data->dokter = $this->dokter ? 1 : 0;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(Nakes $data)
    {
        $this->previous = url()->previous();
        $this->dataPegawai = Pegawai::orderBy('nama')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->dokter = $this->data->dokter == 1 ? true : false;
    }

    public function render()
    {
        return view('livewire.pengaturan.nakes.form');
    }
}
