<?php

namespace App\Livewire\Datamaster\Tariftindakan;

use App\Models\Barang;
use Livewire\Component;
use App\Models\BarangSatuan;
use App\Models\TarifTindakan;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    public $data;
    public $dataBarang = [];
    public $previous;
    public $nama;
    public $icd_10_cm;
    public $kategori = "Medis";
    public $biaya_jasa_dokter = 0;
    public $biaya_jasa_perawat = 0;
    public $biaya_tidak_langsung = 0;
    public $biaya_alat_bahan = 0;
    public $biaya_keuntungan_klinik = 0;
    public $alatBahan = [];

    public function tambahAlatBahan()
    {
        array_push($this->alatBahan, [
            'id' => null,
            'barang_satuan_id' => null,
            'barangSatuan' => [],
            'qty' => 0,
            'harga' => 0,
            'rasio_dari_terkecil' => 0,
        ]);
    }

    public function updatedAlatBahan($value, $key)
    {
        $index = explode('.', $key);
        if ($value) {
            if ($index[1] == 'id') {
                $alatBahan = collect($this->dataBarang)->where('id', $value)->first();
                $barangSatuan = collect($alatBahan['barangSatuan']);
                $this->alatBahan[$index[0]]['id'] = $alatBahan['id'] ?? null;
                $this->alatBahan[$index[0]]['barang_satuan_id'] = null;
                $this->alatBahan[$index[0]]['barangSatuan'] = $barangSatuan->toArray();
                $this->alatBahan[$index[0]]['qty'] = $this->alatBahan[$index[0]]['qty'] ?? 0;
                $this->alatBahan[$index[0]]['rasio_dari_terkecil'] = null;
                $this->alatBahan[$index[0]]['harga'] = 0;
            }

            if ($index[1] == 'barang_satuan_id') {
                $alatBahan = collect($this->dataBarang)->where('id', $this->alatBahan[$index[0]]['id'])->first();
                $barangSatuan = collect($alatBahan['barangSatuan']);
                $selectedSatuan = $barangSatuan->where('id', $this->alatBahan[$index[0]]['barang_satuan_id'])->first();
                $this->alatBahan[$index[0]]['barang_satuan_id'] = $this->alatBahan[$index[0]]['barang_satuan_id'];
                $this->alatBahan[$index[0]]['rasio_dari_terkecil'] = $selectedSatuan['rasio_dari_terkecil'];
                $this->alatBahan[$index[0]]['harga'] = $selectedSatuan['harga_jual'] ?? 0;
            }
        } else {
            $this->alatBahan[$index[0]]['id'] = null;
            $this->alatBahan[$index[0]]['barang_satuan_id'] = null;
            $this->alatBahan[$index[0]]['barangSatuan'] = [];
            $this->alatBahan[$index[0]]['qty'] = 0;
            $this->alatBahan[$index[0]]['rasio_dari_terkecil'] = null;
            $this->alatBahan[$index[0]]['harga'] = 0;
        }
        $harga = (int) ($this->alatBahan[$index[0]]['harga'] ?? 0);
        $qty = (int) ($this->alatBahan[$index[0]]['qty'] ?? 0);
        $this->alatBahan[$index[0]]['sub_total'] = $harga * $qty;
        $this->biaya_alat_bahan = collect($this->alatBahan)->count() > 0 ? collect($this->alatBahan)->sum(fn($q) => $q['sub_total'] ?? 0) : 0;
    }

    public function hapusAlatBahan($key)
    {
        unset($this->alatBahan[$key]);
        $this->alatBahan = array_merge($this->alatBahan);
        $this->biaya_alat_bahan = collect($this->alatBahan)->count() > 0 ? collect($this->alatBahan)->sum(fn($q) => $q['sub_total'] ?? 0) : 0;
    }

    public function submit()
    {
        $this->validate([
            'kategori' => 'required',
            'nama' => 'required',
            'biaya_jasa_dokter' => 'required|numeric',
            'biaya_jasa_perawat' => 'required|numeric',
            'biaya_tidak_langsung' => 'required|numeric',
            'biaya_alat_bahan' => 'required|numeric',
            'biaya_keuntungan_klinik' => 'required|numeric',
        ]);

        DB::transaction(function () {
            $this->data->icd_10_cm = $this->icd_10_cm;
            $this->data->kategori = $this->kategori;
            $this->data->nama = $this->nama;
            $this->data->biaya_jasa_dokter = $this->biaya_jasa_dokter;
            $this->data->biaya_jasa_perawat = $this->biaya_jasa_perawat;
            $this->data->biaya_tidak_langsung = $this->biaya_tidak_langsung;
            $this->data->biaya_keuntungan_klinik = $this->biaya_keuntungan_klinik;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->tarifTindakanAlatBahan()->delete();
            $this->data->tarifTindakanAlatBahan()->insert(collect($this->alatBahan)->map(fn($q) => [
                'barang_id' => $q['id'],
                'tarif_tindakan_id' => $this->data->id,
                'qty' => $q['qty'],
                'barang_satuan_id' => $q['barang_satuan_id'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
            ])->toArray());

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(TarifTindakan $data)
    {
        $this->previous = url()->previous();
        $this->dataBarang = Barang::with('barangSatuan.satuanKonversi')->orderBy('nama')->get()->map(fn($q) => [
            'id' => $q['id'],
            'nama' => $q['nama'],
            'barangSatuan' => $q['barangSatuan']->map(fn($r) => [
                'id' => $r['id'],
                'nama' => $r['nama'],
                'rasio_dari_terkecil' => $r['rasio_dari_terkecil'],
                'konversi_satuan' => $r['konversi_satuan'],
                'harga_jual' => $r['harga_jual'],
                'satuan_konversi' => $r['satuanKonversi'] ? [
                    'id' => $r['satuanKonversi']['id'],
                    'nama' => $r['satuanKonversi']['nama'],
                    'rasio_dari_terkecil' => $r['satuanKonversi']['rasio_dari_terkecil'],
                ] : null,
            ]),
        ])->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        if ($this->data->exists) {
            $this->alatBahan = $this->data->tarifTindakanAlatBahan->map(fn($q) => [
                'id' => $q->barang_id,
                'barang_satuan_id' => $q->barang_satuan_id,
                'harga' => $q->barangSatuan->harga_jual,
                'barangSatuan' => BarangSatuan::where('barang_id', $q->barang_id)->get()->map(fn($r) => [
                    'id' => $r->id,
                    'nama' => $r->nama,
                    'harga_jual' => $r->harga_jual,
                    'rasio_dari_terkecil' => $r->rasio_dari_terkecil,
                    'konversi_satuan' => $r->konversi_satuan,
                ]),
                'qty' => $q->qty,
                'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
                'sub_total' => $q->barangSatuan->harga_jual * $q->qty,
            ])->toArray();
            $this->biaya_alat_bahan = collect($this->alatBahan)->count() > 0 ? collect($this->alatBahan)->sum(fn($q) => $q['sub_total'] ?? 0) : 0;
        }
    }

    public function render()
    {
        return view('livewire.datamaster.tariftindakan.form');
    }
}
