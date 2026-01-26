<?php

namespace App\Livewire\Pengaturan\Nakes;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\KepegawaianPegawai;
use App\Models\Nakes;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataPegawai = [], $kepegawaianPegawai;
    public $nama, $ihs, $nik, $alamat, $no_hp, $dokter = false, $kepegawaian_pegawai_id;

    public function updatedPegawaiId($value)
    {
        $this->reset('nama', 'ihs', 'nik', 'alamat', 'no_hp', 'dokter');
        if ($value) {
            $this->kepegawaianPegawai = KepegawaianPegawai::find($this->kepegawaian_pegawai_id);
            $this->nama = $this->kepegawaianPegawai->nama;
            $this->ihs = $this->kepegawaianPegawai->ihs;
            $this->nik = $this->kepegawaianPegawai->nik;
            $this->alamat = $this->kepegawaianPegawai->alamat;
            $this->no_hp = $this->kepegawaianPegawai->no_hp;
        } else {
            $this->kepegawaianPegawai = null;
        }
    }

    public function submit()
    {
        if (!$this->kepegawaian_pegawai_id) {
            $this->validateWithCustomMessages([
                'nama' => 'required',
                'nik' => 'required',
                'alamat' => 'required',
                'no_hp' => 'required',
            ]);
        }

        DB::transaction(function () {
            $this->data->kepegawaian_pegawai_id = $this->kepegawaian_pegawai_id;
            $this->data->ihs = $this->ihs;
            if (!$this->kepegawaian_pegawai_id) {
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
        $this->redirect('/pengaturan/nakes');
    }

    public function mount(Nakes $data)
    {
        
        $this->dataPegawai = KepegawaianPegawai::orderBy('nama')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->dokter = $this->data->dokter == 1 ? true : false;
    }

    public function render()
    {
        return view('livewire.pengaturan.nakes.form');
    }
}
