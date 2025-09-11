<?php

namespace App\Livewire\Datamaster\Barangdagang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\KodeAkun;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    public $data, $previous, $dataKodeAkun = [];
    public $nama;
    public $satuan;
    public $kode_akun_id;
    public $kfa;
    public $indikasi;
    public $harga;
    public $perlu_resep = 0;
    public $unit_bisnis;
    public $barangSatuan = [];

    public function tambahSatuan()
    {
        $this->barangSatuan[] = [
            'nama' => '',
            'rasio_dari_terkecil' => 0,
            'harga_jual' => 0,
        ];
    }

    public function hapusSatuan($index)
    {
        unset($this->barangSatuan[$index]);
        $this->barangSatuan = array_values($this->barangSatuan);
    }

    public function submit()
    {
        $this->validate([
            'kode_akun_id' => 'required',
            'barangSatuan' => 'required',
            'barangSatuan.*.rasio_dari_terkecil' => 'required|numeric|min:1',
            'barangSatuan.*.harga_jual' => 'required',
            'barangSatuan.*.nama' => 'required',
            'nama' => 'required',
            'unit_bisnis' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->kfa = $this->kfa;
            $this->data->perlu_resep = $this->perlu_resep == 1 ? 1 : 0;
            $this->data->unit_bisnis = $this->unit_bisnis;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $timestamp = now();
            if (!$this->data->exists) {
                $this->data->barangSatuan()->insert([
                    'nama' => $this->satuan,
                    'rasio_dari_terkecil' => 1,
                    'harga_jual' => $this->harga,
                    'barang_id' => $this->data->id,
                    'pengguna_id' => auth()->id(),
                    'utama' => 1,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            } else {
                $this->data->barangSatuan()->where('rasio_dari_terkecil', 1)->update([
                    'harga_jual' => $this->harga,
                    'pengguna_id' => auth()->id(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(Barang $data)
    {
        $this->previous = url()->previous();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->barangSatuan = $this->data->barangSatuan->sortByDesc('rasio_dari_terkecil')->toArray();
        if (!$data->exists) {
            $this->barangSatuan[] = [
                'nama' => null,
                'rasio_dari_terkecil' => 1,
                'harga_jual' => 0
            ];
        } else {
            $this->dataKodeAkun = KodeAkun::detail()->where('kategori', 'Aktiva')->get()->toArray();
            $this->satuan = $this->data->barangSatuanTerkecil->nama;
            $this->harga = $this->data->barangSatuanTerkecil->harga_jual;
        }
        $this->dataKodeAkun = KodeAkun::detail()->where('kategori', 'Aktiva')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.datamaster.barangdagang.form');
    }
}
