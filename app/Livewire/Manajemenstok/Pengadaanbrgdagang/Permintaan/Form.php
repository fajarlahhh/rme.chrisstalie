<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Permintaan;

use App\Models\Barang;
use Livewire\Component;
use App\Models\Pengguna;
use App\Class\BarangClass;
use App\Models\VerifikasiPengadaan;
use Illuminate\Support\Str;
use App\Models\BarangSatuan;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanPengadaan; 
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [], $dataPengguna = [], $barang = [];
    public $deskripsi, $data, $verifikator_id;

    public function submit()
    {
        $this->validateWithCustomMessages([
            'deskripsi' => 'required',
            'barang' => 'required|array',
            'barang.*.id' => 'required',
            'barang.*.qty' => 'required',
        ]);

        DB::transaction(function () {
            

            $this->data->deskripsi = $this->deskripsi;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->permintaanPengadaanDetail()->delete();

            $this->data->permintaanPengadaanDetail()->insert(collect($this->barang)->map(function ($q) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty_permintaan' => $q['qty'],
                    'permintaan_pengadaan_id' => $this->data->id,
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                    'barang_id' => $brg['barang_id'],
                ];
            })->toArray()); 

            if ($this->verifikator_id) {
                $verifikasiPengadaan = new VerifikasiPengadaan();
                $verifikasiPengadaan->permintaan_pengadaan_id = $this->data->id;
                $verifikasiPengadaan->jenis = 'Permintaan Pengadaan';
                $verifikasiPengadaan->pengguna_id = $this->verifikator_id;
                $verifikasiPengadaan->save();
            }

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/manajemenstok/pengadaanbrgdagang/permintaan');
    }

    public function mount(PermintaanPengadaan $data)
    {
        
        $this->dataBarang = BarangClass::getBarangBySatuanUtama('Apotek');
        $this->dataPengguna = Pengguna::with('pegawai')->where(fn($q) => $q->whereHas('permissions', function ($q) {
            $q->where('name', 'pengadaanbrg.dagangverifikasi');
        })->orWhere(fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', 'administrator'))))->orderBy('nama')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        if ($this->data->exists) {
            # code...
            $this->barang = $this->data->permintaanPengadaanDetail->map(fn($q) => [
                'id' => $q->barang_satuan_id,
                'barang_id' => $q->barang_id,
                'qty' => $q->qty_permintaan,
            ])->toArray();
        }
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.permintaan.form');
    }
}
