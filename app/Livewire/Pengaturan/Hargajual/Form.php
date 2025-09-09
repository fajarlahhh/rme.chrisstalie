<?php

namespace App\Livewire\Pengaturan\Hargajual;

use App\Models\Barang;
use Livewire\Component;
use App\Models\BarangSatuan;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    public $data;
    public $previous;
    public $barang_id;
    public $nama;
    public $harga_jual;
    public $faktor_konversi;
    public $satuan_konversi_id;
    public $jenis = "Obat";
    public $dataBarang = [];
    public $dataBarangSatuan = [];

    public function submit()
    {
        $this->validate([
            'barang_id' => 'required|numeric',
            'nama' => 'required',
            'harga_jual' => 'required|numeric',
        ]);

        if ($this->data->rasio_dari_terkecil != 1) {
            $this->validate([
                'faktor_konversi' => 'required|numeric',
                'satuan_konversi_id' => 'required|numeric',
            ]);
        }

        DB::transaction(function () {
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
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function updatedBarangId()
    {
        $this->dataBarangSatuan = BarangSatuan::where('barang_id', $this->barang_id)->with(['barang.barangSatuanTerkecil'])->orderBy('rasio_dari_terkecil', 'desc')->get()->toArray();
    }

    public function mount(BarangSatuan $data)
    {
        $this->data = $data;

        $this->previous = url()->previous();
        $this->fill($data->toArray());
        $this->dataBarang = Barang::persediaan()->with(['barangSatuanTerkecil'])->orderBy('nama')->get()->toArray();
        if ($data->rasio_dari_terkecil != 1) {
            $this->dataBarangSatuan = BarangSatuan::where('barang_id', $data->barang_id)->with(['barang.barangSatuanTerkecil'])->orderBy('rasio_dari_terkecil', 'desc')->get()->toArray();
        }
    }

    public function render()
    {
        return view('livewire.pengaturan.hargajual.form');
    }
}
