<?php

namespace App\Livewire\Pengaturan\Hargajual;

use App\Models\Barang;
use Livewire\Component;
use App\Models\BarangSatuan;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data;
    
    public $barang_id;
    public $nama;
    public $harga_jual;
    public $utama;
    public $faktor_konversi;
    public $satuan_konversi_id;
    public $dataBarang = [];
    public $dataBarangSatuan = [];

    public function submit()
    {
        $this->validateWithCustomMessages([
            'barang_id' => 'required|numeric',
            'nama' => 'required',
            'harga_jual' => 'required|numeric',
        ]);

        if ($this->data->rasio_dari_terkecil != 1) {
            $this->validateWithCustomMessages([
                'faktor_konversi' => 'required|numeric',
                'satuan_konversi_id' => 'required|numeric',
            ]);
        }

        DB::transaction(function () {
            if ($this->utama == 1) {
                BarangSatuan::where('barang_id', $this->barang_id)->where('id', '!=', $this->data->id)->update(['utama' => 0]);
            }
            $barangSatuan = collect($this->dataBarangSatuan)->where('id', $this->satuan_konversi_id)->first();
            if ($this->data->rasio_dari_terkecil != 1) {
                $this->data->nama = $this->nama;
                $this->data->rasio_dari_terkecil = $this->faktor_konversi * (
                    $barangSatuan['rasio_dari_terkecil'] *
                    $barangSatuan['barang']['barang_satuan_terkecil']['rasio_dari_terkecil']);
                $this->data->satuan_konversi_id = $this->satuan_konversi_id;
            }
            $this->data->barang_id = $this->barang_id;
            $this->data->harga_jual = $this->harga_jual;
            $this->data->utama = $this->utama;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/pengaturan/hargajual');
    }

    public function updatedBarangId()
    {
        $this->dataBarangSatuan = BarangSatuan::where('barang_id', $this->barang_id)->with(['barang.barangSatuanTerkecil'])->orderBy('rasio_dari_terkecil', 'desc')->get()->toArray();
    }

    public function mount(BarangSatuan $data)
    {
        $this->data = $data;

        
        $this->fill($data->toArray());
        $this->dataBarang = Barang::with(['barangSatuanTerkecil'])->orderBy('nama')->get()->toArray();
        if ($data->rasio_dari_terkecil != 1) {
            $this->dataBarangSatuan = BarangSatuan::where('barang_id', $data->barang_id)->with(['barang.barangSatuanTerkecil'])->orderBy('rasio_dari_terkecil', 'desc')->get()->toArray();
        }

        if ($data->exists && $data->rasio_dari_terkecil != 1) {
            $this->faktor_konversi = $data->rasio_dari_terkecil / $data->satuanKonversi->rasio_dari_terkecil;
            $this->utama = $data->utama == 1 ? 1 : 0;
        }
    }

    public function render()
    {
        return view('livewire.pengaturan.hargajual.form');
    }
}
