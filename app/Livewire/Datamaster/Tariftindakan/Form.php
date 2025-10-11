<?php

namespace App\Livewire\Datamaster\Tariftindakan;

use App\Models\Barang;
use Livewire\Component;
use App\Models\BarangSatuan;
use App\Models\TarifTindakan;
use Illuminate\Support\Facades\DB;
use App\Models\KodeAkun;
use App\Models\Aset;

class Form extends Component
{
    public $data;
    public $dataBarang = [], $dataKodeAkun = [], $dataAset = [];
    public $previous;
    public $nama;
    public $kode_akun_id;
    public $icd_9_cm;
    public $biaya_jasa_dokter = 0;
    public $biaya_jasa_perawat = 0;
    public $biaya_bahan = 0;
    public $biaya_alat = 0;
    public $tarif = 0;
    public $alatBarang = [];
    public $alat = [];
    public $bahan = [];

    public function submit()
    {
        $this->validate([
            'kode_akun_id' => 'required',
            'nama' => 'required',
            'biaya_jasa_dokter' => 'required|numeric',
            'biaya_jasa_perawat' => 'required|numeric',
            'tarif' => 'required|numeric',
        ]);

        DB::transaction(function () {
            $alatBahan = collect(collect($this->alat)->map(fn($q) => [
                'barang_id' => null,
                'aset_id' => $q['id'],
                'tarif_tindakan_id' => $this->data->id,
                'qty' => $q['qty'],
                'jenis' => 'Alat',
                'barang_satuan_id' => null,
                'rasio_dari_terkecil' => null,
                'biaya' => $q['biaya'],
            ]))->merge(collect($this->bahan)->map(fn($q) => [
                'barang_id' => $q['id'],
                'aset_id' => null,
                'tarif_tindakan_id' => $this->data->id,
                'qty' => $q['qty'],
                'jenis' => 'Barang',
                'barang_satuan_id' => $q['barang_satuan_id'],
                'rasio_dari_terkecil' => null,
                'biaya' => $q['biaya'],
            ]));

            dd($alatBahan);

            $this->data->icd_9_cm = $this->icd_9_cm;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->nama = $this->nama;
            $this->data->biaya_jasa_dokter = $this->biaya_jasa_dokter;
            $this->data->biaya_jasa_perawat = $this->biaya_jasa_perawat;
            $this->data->tarif = $this->tarif;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->tarifTindakanAlatBarang()->delete();
            $this->data->tarifTindakanAlatBarang()->insert(collect($alatBahan)->map(fn($q) => [
                'barang_id' => $q['barang_id'],
                'aset_id' => $q['aset_id'],
                'tarif_tindakan_id' => $this->data->id,
                'qty' => $q['qty'],
                'barang_satuan_id' => $q['barang_satuan_id'],
                'rasio_dari_terkecil' => $q['jenis'] == 'Barang' ? null : $q['rasio_dari_terkecil'],
                'biaya' => $q['biaya'],
            ])->toArray());

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(TarifTindakan $data)
    {
        $this->previous = url()->previous();
        $this->dataBarang = BarangSatuan::with('barang', 'satuanKonversi')->orderBy('nama')->get()->map(fn($q) => [
            'id' => $q['barang_id'],
            'nama' => $q['barang']['nama'],
            'barang_satuan_id' => $q['barang_satuan_id'],
            'biaya' => $q['harga_jual'],
            'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
            'konversi_satuan' => $q['konversi_satuan'],
            'satuan' => $q['nama'],
            'satuan_konversi' => isset($q['satuanKonversi']) ? $q['satuanKonversi']['nama'] : null,
            'satuan_konversi_rasio_dari_terkecil' => isset($q['satuanKonversi']) ? $q['satuanKonversi']['rasio_dari_terkecil'] : null,
        ])->toArray();
        $this->dataAset = Aset::where('kode_akun_id', '15130')->orderBy('nama')->get()->toArray();
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '42000')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        if ($this->data->exists) {
            $this->alatBarang = $this->data->tarifTindakanAlatBarang->map(fn($q) => [
                'barang_id' => $q->barang_id,
                'aset_id' => $q->aset_id,
                'jenis' => $q->barang_id ? 'Barang' : 'Alat',
                'barang_satuan_id' => $q->barang_satuan_id,
                'biaya' => $q->jenis == 'Barang' ? $q->barangSatuan?->biaya : $q->biaya,
                'barangSatuan' => BarangSatuan::where('barang_id', $q->barang_id)->get()->map(fn($r) => [
                    'id' => $r->id,
                    'nama' => $r->nama,
                    'harga_jual' => $r->harga_jual,
                    'rasio_dari_terkecil' => $r->rasio_dari_terkecil,
                    'konversi_satuan' => $r->konversi_satuan,
                ]),
                'qty' => $q->qty,
                'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
                'sub_total' => $q->jenis == 'Barang' ? $q->barangSatuan?->harga_jual * $q->qty : $q->biaya * $q->qty,
            ])->toArray();
            $this->biaya_bahan = collect($this->alatBarang)->where('jenis', 'Barang')->sum(fn($q) => $q['sub_total'] ?? 0);
            $this->biaya_alat = collect($this->alatBarang)->where('jenis', 'Alat')->sum(fn($q) => $q['sub_total'] ?? 0);
        }
    }

    public function render()
    {
        return view('livewire.datamaster.tariftindakan.form');
    }
}
