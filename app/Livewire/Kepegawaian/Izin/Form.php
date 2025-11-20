<?php

namespace App\Livewire\Kepegawaian\Izin;

use Livewire\Component;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataPegawai = [];
    public $pegawai_id, $tanggal, $keterangan, $izin;

    public function mount()
    {
        $this->dataPegawai = Pegawai::orderBy('nama')->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'pegawai_id' => ['required'],
            'tanggal' => ['required'],
            'keterangan' => 'required',
        ]);
        if (Absensi::where('id', $this->tanggal . '-' . $this->pegawai_id)->exists()) {
            session()->flash('danger', 'Data sudah ada');
        } else {
            $data = new Absensi();
            $data->id = $this->tanggal . '-' . $this->pegawai_id;
            $data->tanggal = $this->tanggal;
            $data->izin = $this->izin;
            $data->keterangan = $this->keterangan;
            $data->pegawai_id = $this->pegawai_id;
            $data->pengguna_id = auth()->id();
            $data->save();
            session()->flash('success', 'Berhasil menyimpan data');
            return redirect()->to('/kepegawaian/izin');
        }
    }

    public function render()
    {
        return view('livewire.kepegawaian.izin.form');
    }
}
