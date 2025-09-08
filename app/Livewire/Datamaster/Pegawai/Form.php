<?php

namespace App\Livewire\Datamaster\Pegawai;

use Livewire\Component;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    public $data, $previous;
    public $nama, $alamat, $no_hp, $tanggal_masuk, $tanggal_lahir, $jenis_kelamin, $nik, $npwp, $no_bpjs, $gaji, $tunjangan, $tunjangan_transport, $tunjangan_bpjs, $office, $satuan_tugas, $status, $kantor;

    public function submit()
    {
        $this->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required|date',
            'tanggal_masuk' => 'required|date',
            'nik' => 'required|numeric|digits:16',
            'no_bpjs' => 'required',
            'gaji' => 'required|numeric',
            'tunjangan' => 'required|numeric',
            'tunjangan_transport' => 'required|numeric',
            'kantor' => 'required',
        ]);
        
        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->alamat = $this->alamat;
            $this->data->no_hp = $this->no_hp;
            $this->data->tanggal_lahir = $this->tanggal_lahir;
            $this->data->tanggal_masuk = $this->tanggal_masuk;
            $this->data->jenis_kelamin = $this->jenis_kelamin;
            $this->data->nik = $this->nik;
            $this->data->npwp = $this->npwp;
            $this->data->no_bpjs = $this->no_bpjs;
            $this->data->gaji = $this->gaji;
            $this->data->tunjangan = $this->tunjangan;
            $this->data->tunjangan_transport = $this->tunjangan_transport;
            $this->data->tunjangan_bpjs = $this->tunjangan_bpjs;
            $this->data->satuan_tugas = $this->satuan_tugas;
            $this->data->status = $this->status == 'Aktif' ? 'Aktif' : 'Non Aktif';
            $this->data->pengguna_id = auth()->id();
            $this->data->kantor = $this->kantor;
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(Pegawai $data)
    {
        $this->previous = url()->previous();
        $this->data = $data;
        $this->fill($this->data->toArray());
    }

    public function render()
    {
        return view('livewire.datamaster.pegawai.form');
    }
}
