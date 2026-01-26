<?php

namespace App\Livewire\Kepegawaian\Izin;

use Livewire\Component;
use App\Models\KepegawaianPegawai;
use App\Models\KepegawaianAbsensi;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataPegawai = [];
    public $kepegawaian_pegawai_id, $tanggal, $keterangan, $izin;

    public function mount()
    {
        $this->dataPegawai = KepegawaianPegawai::orderBy('nama')->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'kepegawaian_pegawai_id' => ['required'],
            'tanggal' => ['required'],
            'keterangan' => 'required',
        ]);
        if (KepegawaianAbsensi::where('id', $this->tanggal . '-' . $this->kepegawaian_pegawai_id)->exists()) {
            session()->flash('danger', 'Data sudah ada');
        } else {
            $data = new KepegawaianAbsensi();
            $data->id = $this->tanggal . '-' . $this->kepegawaian_pegawai_id;
            $data->tanggal = $this->tanggal;
            $data->izin = $this->izin;
            $data->keterangan = $this->keterangan;
            $data->kepegawaian_pegawai_id = $this->kepegawaian_pegawai_id;
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
