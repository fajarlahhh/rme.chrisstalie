<?php

namespace App\Livewire\Datamaster\Pegawai;

use Livewire\Component;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use App\Models\UnsurGaji;

class Form extends Component
{
    public $data, $previous, $unsurGaji = [];
    public $nama, $alamat, $no_hp, $tanggal_masuk, $tanggal_lahir, $jenis_kelamin, $nik, $npwp, $no_bpjs, $gaji, $tunjangan, $tunjangan_transport, $tunjangan_bpjs, $office, $satuan_tugas, $status;

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
            $this->data->satuan_tugas = $this->satuan_tugas;
            if ($this->data->exists) {
                $this->data->status = $this->status == 'Aktif' ? 'Aktif' : 'Non Aktif';
            } else {
                $this->data->status = 'Aktif';
            }
            $this->data->status = $this->status == 'Aktif' ? 'Aktif' : 'Non Aktif';
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->pegawaiUnsurGaji()->delete();
            $this->data->pegawaiUnsurGaji()->insert(collect($this->unsurGaji)->where('nilai', '>', 0)->map(fn($q) => [
                'pegawai_id' => $this->data->id,
                'unsur_gaji_kode_akun_id' => $q['unsur_gaji_kode_akun_id'],
                'unsur_gaji_nama' => $q['unsur_gaji_nama'],
                'unsur_gaji_sifat' => $q['unsur_gaji_sifat'],
                'nilai' => $q['nilai'],
            ])->toArray());
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(Pegawai $data)
    {
        $this->previous = url()->previous();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $dataUnsurGaji =  UnsurGaji::all()->map(fn($q) => [
            'unsur_gaji_nama' => $q['nama'],
            'unsur_gaji_sifat' => $q['sifat'],
            'unsur_gaji_kode_akun_id' => $q['kode_akun_id'],
        ]);
        $pegawaiUnsurGaji = $this->data->pegawaiUnsurGaji->map(fn($q) => [
            'unsur_gaji_nama' => $q['unsur_gaji_nama'],
            'unsur_gaji_sifat' => $q['unsur_gaji_sifat'],
            'unsur_gaji_kode_akun_id' => $q['unsur_gaji_kode_akun_id'],
        ]);

        $mergedUnsurGaji = $dataUnsurGaji
            ->merge($pegawaiUnsurGaji)
            ->unique('unsur_gaji_kode_akun_id')
            ->values();
        $dataUnsurGajiPegawai = [];
        foreach ($mergedUnsurGaji as $key => $value) {
            $dataUnsurGajiPegawai[] =[
                'unsur_gaji_nama' => $value['unsur_gaji_nama'],
                'unsur_gaji_sifat' => $value['unsur_gaji_sifat'],
                'unsur_gaji_kode_akun_id' => $value['unsur_gaji_kode_akun_id'],
                'nilai' => $this->data->pegawaiUnsurGaji->where('unsur_gaji_kode_akun_id', $value['unsur_gaji_kode_akun_id'])->first()?->nilai ?? 0,
            ];
        }
        $this->unsurGaji = $dataUnsurGajiPegawai;
    }

    public function render()
    {
        return view('livewire.datamaster.pegawai.form');
    }
}
