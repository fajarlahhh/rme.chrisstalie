<?php

namespace App\Livewire\Datamaster\Tariftindakan;

use App\Models\Aset;
use App\Models\Barang;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Class\BarangClass;
use App\Models\BarangSatuan;
use App\Models\TarifTindakan;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data;
    public $dataBarang = [], $dataKodeAkun = [], $dataAlat = [];
    
    public $nama;
    public $kode_akun_id;
    public $icd_9_cm;
    public $biaya_jasa_dokter = 0;
    public $biaya_jasa_perawat = 0;
    public $biaya_barang = 0;
    public $biaya_alat = 0;
    public $tarif = 0;
    public $alatBarang = [];
    public $alat = [];
    public $barang = [];

    public function submit()
    {
        $this->validateWithCustomMessages([
            'kode_akun_id' => 'required',
            'nama' => 'required',
            'biaya_jasa_dokter' => 'required|numeric',
            'biaya_jasa_perawat' => 'required|numeric',
            'tarif' => 'required|numeric',
        ]);
        DB::transaction(function () {

            $this->data->icd_9_cm = $this->icd_9_cm;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->nama = $this->nama;
            $this->data->biaya_jasa_dokter = $this->biaya_jasa_dokter;
            $this->data->biaya_jasa_perawat = $this->biaya_jasa_perawat;
            $this->data->tarif = $this->tarif;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $alatBahan = collect(collect($this->alat)->map(fn($q) => [
                'aset_id' => $q['id'],
                'tarif_tindakan_id' => $this->data->id,
                'qty' => $q['qty'],
                'jenis' => 'Alat',
                'barang_satuan_id' => null,
                'biaya' => $q['biaya'],
            ]))->merge(collect($this->barang)->map(fn($q) => [
                'aset_id' => null,
                'tarif_tindakan_id' => $this->data->id,
                'qty' => $q['qty'],
                'jenis' => 'Barang',
                'barang_satuan_id' => $q['id'],
                'biaya' => $q['biaya'],
            ]));

            $this->data->tarifTindakanAlatBarang()->delete();
            $this->data->tarifTindakanAlatBarang()->insert(collect($alatBahan)->map(fn($q) => [
                'aset_id' => $q['aset_id'] != '' ? $q['aset_id'] : null,
                'tarif_tindakan_id' => $this->data->id,
                'qty' => $q['qty'],
                'barang_satuan_id' => $q['barang_satuan_id'] != '' ? $q['barang_satuan_id'] : null,
                'biaya' => $q['biaya'],
            ])->toArray());

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/datamaster/tariftindakan');
    }

    public function mount(TarifTindakan $data)
    {
        
        $this->dataBarang = BarangClass::getBarang('klinik');
        $this->dataAlat = Aset::where('kode_akun_id', '15130')->orderBy('nama')->get()->toArray();
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '42000')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        if ($this->data->exists) {
            $this->barang = $this->data->tarifTindakanAlatBarang->whereNotNull('barang_satuan_id')->whereIn('barang_satuan_id', collect($this->dataBarang)->pluck('id'))->values()->map(fn($q) => [
                'id' => $q->barang_satuan_id,
                'biaya' => collect($this->dataBarang)->firstWhere('id', $q->barang_satuan_id)['biaya'],
                'qty' => $q->qty,
                'subtotal' => collect($this->dataBarang)->firstWhere('id', $q->barang_satuan_id)['biaya'] * $q->qty,
            ])->toArray();
            $this->alat = $this->data->tarifTindakanAlatBarang->whereNotNull('aset_id')->values()->map(fn($q) => [
                'id' => $q->aset_id,
                'biaya' => $q->biaya,
                'qty' => $q->qty,
                'subtotal' => $q->biaya * $q->qty,
            ])->toArray();
        }
    }

    public function render()
    {
        return view('livewire.datamaster.tariftindakan.form');
    }
}
