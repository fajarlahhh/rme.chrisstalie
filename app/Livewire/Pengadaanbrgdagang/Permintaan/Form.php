<?php

namespace App\Livewire\Pengadaanbrgdagang\Permintaan;

use App\Models\Barang;
use Livewire\Component;
use App\Models\Pengguna;
use App\Class\BarangClass;
use App\Models\Verifikasi;
use Illuminate\Support\Str;
use App\Models\BarangSatuan;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanPembelian; 
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
            
            if (!$this->data->exists) {
                $this->data->id = Str::uuid();
            }

            $this->data->deskripsi = $this->deskripsi;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->permintaanPembelianDetail()->delete();

            $this->data->permintaanPembelianDetail()->insert(collect($this->barang)->map(function ($q) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty_permintaan' => $q['qty'],
                    'permintaan_pembelian_id' => $this->data->id,
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                ];
            })->toArray()); 

            if ($this->verifikator_id) {
                $verifikasi = new Verifikasi();
                $verifikasi->id = Str::uuid();
                $verifikasi->referensi_id = $this->data->id;
                $verifikasi->jenis = 'Permintaan Pembelian';
                $verifikasi->pengguna_id = $this->verifikator_id;
                $verifikasi->save();
            }

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('pengadaanbrgdagang/permintaan');
    }

    public function mount(PermintaanPembelian $data)
    {
        
        $this->dataBarang = BarangClass::getBarangBySatuanUtama('Apotek');
        $this->dataPengguna = Pengguna::where(fn($q) => $q->whereHas('permissions', function ($q) {
            $q->where('name', 'pengadaanverifikasi');
        })->orWhere(fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', 'administrator'))))->orderBy('nama')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        if ($this->data->exists) {
            # code...
            $this->barang = $this->data->permintaanPembelianDetail->map(fn($q) => [
                'id' => $q->barang_satuan_id,
                'qty' => $q->qty_permintaan,
            ])->toArray();
        }
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.permintaan.form');
    }
}
