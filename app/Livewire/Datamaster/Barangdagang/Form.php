<?php

namespace App\Livewire\Datamaster\Barangdagang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\BarangSatuan;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataKodeAkun = [], $dataKodeAkunPenjualan = [], $dataKodeAkunModal = [];
    public $nama;
    public $satuan;
    public $kode_akun_id;
    public $kfa;
    public $kode_akun_penjualan_id;
    public $kode_akun_modal_id;
    public $indikasi;
    public $harga;
    public $perlu_resep = 0;
    public $persediaan = 'Apotek';
    public $barangSatuan = [];
    public $khusus = 0;


    // public function updatedPersediaan()
    // {
    //     if ($this->persediaan == 'Apotek') {
    //     } else {
    //         $this->dataKodeAkunPenjualan = KodeAkun::detail()->where('parent_id', '50000')->get()->toArray();
    //     }
    // }
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
        $this->validateWithCustomMessages([
            'kode_akun_id' => 'required',
            'kode_akun_penjualan_id' => $this->persediaan == 'Apotek' ? 'required' : '',
            'kode_akun_modal_id' => 'required',
            'satuan' => 'required',
            'harga' => 'required|numeric',
            'nama' => 'required',
            'persediaan' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->kfa = $this->kfa;
            $this->data->perlu_resep = $this->perlu_resep == 1 ? 1 : 0;
            $this->data->persediaan = $this->persediaan;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->khusus = $this->khusus;
            $this->data->kode_akun_penjualan_id = $this->kode_akun_penjualan_id;
            $this->data->kode_akun_modal_id = $this->kode_akun_modal_id;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $timestamp = now();
            if (!$this->data->wasRecentlyCreated) {
                $this->data->barangSatuan()->where('rasio_dari_terkecil', 1)->update([
                    'harga_jual' => $this->harga,
                    'pengguna_id' => auth()->id(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            } else {
                $barangSatuan = new BarangSatuan();
                $barangSatuan->nama = $this->satuan;
                $barangSatuan->rasio_dari_terkecil = 1;
                $barangSatuan->harga_jual = $this->harga;
                $barangSatuan->barang_id = $this->data->id;
                $barangSatuan->pengguna_id = auth()->id();
                $barangSatuan->utama = 1;
                $barangSatuan->created_at = $timestamp;
                $barangSatuan->updated_at = $timestamp;
                $barangSatuan->save();
            }
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/datamaster/barangdagang');
    }

    public function mount(Barang $data)
    {
        
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
            $this->satuan = $this->data->barangSatuanTerkecil->nama;
            $this->harga = $this->data->barangSatuanTerkecil->harga_jual;
        }
        $this->dataKodeAkun = KodeAkun::detail()->get()->toArray();
    }

    public function render()
    {
        return view('livewire.datamaster.barangdagang.form');
    }
}
