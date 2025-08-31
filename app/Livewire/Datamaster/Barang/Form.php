<?php

namespace App\Livewire\Datamaster\Barang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    public $data;
    public $previous;
    public $nama;
    public $satuan;
    public $bentuk;
    public $harga_jual;
    public $jenis = "Obat";
    public $golongan;
    public $kfa;
    public $indikasi;
    public $kontraindikasi;
    public $perlu_resep = 0;
    public $garansi;


    public function submit()
    {
        if ($this->jenis == 'Obat') {
            $this->validate([
                'jenis' => 'required',
                'nama' => 'required',
                'satuan' => 'required',
                'harga_jual' => 'required',
                'bentuk' => 'required',
                'golongan' => 'required',
            ]);
        } else {
            $this->validate([
                'jenis' => 'required',
                'nama' => 'required',
                'satuan' => 'required',
                'harga_jual' => 'required',
            ]);
        }

        DB::transaction(function () {
            $this->data->jenis = $this->jenis;
            $this->data->nama = $this->nama;
            $this->data->satuan = $this->satuan;
            $this->data->harga_jual = $this->harga_jual;
            $this->data->bentuk = $this->jenis == 'Obat' ? $this->bentuk : null;
            $this->data->golongan = $this->jenis == 'Obat' ? $this->golongan : null;
            $this->data->kfa = $this->jenis == 'Obat' ? $this->kfa : null;
            $this->data->indikasi = $this->jenis == 'Obat' ? $this->indikasi : null;
            $this->data->kontraindikasi = $this->jenis == 'Obat' ? $this->kontraindikasi : null;
            $this->data->perlu_resep = $this->jenis == 'Obat' ? $this->perlu_resep : null;
            $this->data->garansi = $this->jenis == 'Alat Kesehatan' ? $this->garansi : null;
            $this->data->kantor = 'Apotek';
            $this->data->pengguna_id = auth()->id();
            $this->data->save();
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(Barang $data)
    {
        $this->previous = url()->previous();
        $this->data = $data;
        $this->fill($this->data->toArray());
    }

    public function updatedJenis()
    {
        if ($this->jenis == 'Obat') {
            $this->bentuk = null;
            $this->golongan = null;
            $this->kfa = null;
            $this->indikasi = null;
            $this->kontraindikasi = null;
            $this->perlu_resep = 0;
        }
        if ($this->jenis == 'Alat Kesehatan') {
            $this->garansi = null;
        }
    }

    public function render()
    {
        return view('livewire.datamaster.barang.form');
    }
}
