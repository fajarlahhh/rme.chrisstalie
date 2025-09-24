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
    public $biaya_tidak_langsung = 0;
    public $biaya_alat_bahan = 0;
    public $biaya_keuntungan_klinik = 0;
    public $tarif = 0;
    public $alatBahan = [];

    public function tambahAlatBahan($jenis)
    {
        array_push($this->alatBahan, [
            'id' => null,
            'jenis' => $jenis,
            'barang_satuan_id' => null,
            'barangSatuan' => [],
            'qty' => 0,
            'harga_jual' => 0,
            'rasio_dari_terkecil' => 0,
        ]);
    }

    public function updatedAlatBahan($value, $key)
    {
        $index = explode('.', $key);
        if ($value) {
            if ($index[1] == 'id') {
                if ($this->alatBahan[$index[0]]['jenis'] == 'Bahan') {
                    $alatBahan = collect($this->dataBarang)->where('id', $value)->first();
                    $barangSatuan = collect($alatBahan['barangSatuan']);
                    $this->alatBahan[$index[0]]['id'] = $alatBahan['id'] ?? null;
                    $this->alatBahan[$index[0]]['barang_satuan_id'] = null;
                    $this->alatBahan[$index[0]]['barangSatuan'] = $barangSatuan->toArray();
                    $this->alatBahan[$index[0]]['qty'] = $this->alatBahan[$index[0]]['qty'] ?? 0;
                    $this->alatBahan[$index[0]]['rasio_dari_terkecil'] = null;
                    $this->alatBahan[$index[0]]['harga_jual'] = 0;
                }
                if ($this->alatBahan[$index[0]]['jenis'] == 'Alat') {
                    $alatBahan = collect($this->dataAset)->where('id', $value)->first();
                    $this->alatBahan[$index[0]]['id'] = $alatBahan['id'] ?? null;
                    $this->alatBahan[$index[0]]['harga_jual'] = $alatBahan['metode_penyusutan'] == 'Satuan Hasil Produksi' ? $alatBahan['harga_perolehan'] / $alatBahan['masa_manfaat'] : 0;
                }
            }

            if ($index[1] == 'barang_satuan_id') {
                if ($this->alatBahan[$index[0]]['jenis'] == 'Bahan') {
                    $alatBahan = collect($this->dataBarang)->where('id', $this->alatBahan[$index[0]]['id'])->first();
                    $barangSatuan = collect($alatBahan['barangSatuan']);
                    $selectedSatuan = $barangSatuan->where('id', $this->alatBahan[$index[0]]['barang_satuan_id'])->first();
                    $this->alatBahan[$index[0]]['barang_satuan_id'] = $this->alatBahan[$index[0]]['barang_satuan_id'];
                    $this->alatBahan[$index[0]]['rasio_dari_terkecil'] = $selectedSatuan['rasio_dari_terkecil'];
                    $this->alatBahan[$index[0]]['harga_jual'] = $selectedSatuan['harga_jual'] ?? 0;
                }
            }
        } else {
            if ($this->alatBahan[$index[0]]['jenis'] == 'Bahan') {
                $this->alatBahan[$index[0]]['id'] = null;
                $this->alatBahan[$index[0]]['barang_satuan_id'] = null;
                $this->alatBahan[$index[0]]['barangSatuan'] = [];
                $this->alatBahan[$index[0]]['qty'] = 0;
                $this->alatBahan[$index[0]]['rasio_dari_terkecil'] = null;
                $this->alatBahan[$index[0]]['harga_jual'] = 0;
            }
        }
        if ($this->alatBahan[$index[0]]['jenis'] == 'Bahan') {
            $harga = (int) ($this->alatBahan[$index[0]]['harga_jual'] ?? 0);
            $qty = (int) ($this->alatBahan[$index[0]]['qty'] ?? 0);
            $this->alatBahan[$index[0]]['sub_total'] = $harga * $qty;
            $this->biaya_alat_bahan = collect($this->alatBahan)->count() > 0 ? collect($this->alatBahan)->sum(fn($q) => $q['sub_total'] ?? 0) : 0;
        }
        if ($this->alatBahan[$index[0]]['jenis'] == 'Alat') {
            $harga = (int) ($this->alatBahan[$index[0]]['harga_jual'] ?? 0);
            $qty = (int) ($this->alatBahan[$index[0]]['qty'] ?? 0);
            $this->alatBahan[$index[0]]['sub_total'] = $harga * $qty;
            $this->biaya_alat_bahan = collect($this->alatBahan)->count() > 0 ? collect($this->alatBahan)->sum(fn($q) => $q['sub_total'] ?? 0) : 0;
        }
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
            'kode_akun_id' => 'required',
            'nama' => 'required',
            'biaya_jasa_dokter' => 'required|numeric',
            'biaya_jasa_perawat' => 'required|numeric',
            'biaya_tidak_langsung' => 'required|numeric',
            'biaya_alat_bahan' => 'required|numeric',
            'tarif' => 'required|numeric',
        ]);

        DB::transaction(function () {
            $this->data->icd_9_cm = $this->icd_9_cm;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->nama = $this->nama;
            $this->data->biaya_jasa_dokter = $this->biaya_jasa_dokter;
            $this->data->biaya_jasa_perawat = $this->biaya_jasa_perawat;
            $this->data->biaya_tidak_langsung = $this->biaya_tidak_langsung;
            $this->data->tarif = $this->tarif;

            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->tarifTindakanAlatBahan()->delete();
            $this->data->tarifTindakanAlatBahan()->insert(collect($this->alatBahan)->map(fn($q) => [
                'barang_id' => $q['id'],
                'tarif_tindakan_id' => $this->data->id,
                'jenis' => $q['jenis'],
                'qty' => $q['qty'],
                'barang_satuan_id' => $q['barang_satuan_id'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
                'harga_jual' => $q['harga_jual'],
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
        $this->dataAset = Aset::where('kode_akun_id', '15130')->orderBy('nama')->get()->toArray();
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '42000')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        if ($this->data->exists) {
            $this->alatBahan = $this->data->tarifTindakanAlatBahan->map(fn($q) => [
                'id' => $q->barang_id,
                'jenis' => $q->jenis,
                'barang_satuan_id' => $q->barang_satuan_id,
                'harga_jual' => $q->barangSatuan?->harga_jual,
                'barangSatuan' => BarangSatuan::where('barang_id', $q->barang_id)->get()->map(fn($r) => [
                    'id' => $r->id,
                    'nama' => $r->nama,
                    'harga_jual' => $r->harga_jual,
                    'rasio_dari_terkecil' => $r->rasio_dari_terkecil,
                    'konversi_satuan' => $r->konversi_satuan,
                ]),
                'qty' => $q->qty,
                'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
                'sub_total' => $q->barangSatuan?->harga_jual * $q->qty,
            ])->toArray();
            $this->biaya_alat_bahan = collect($this->alatBahan)->count() > 0 ? collect($this->alatBahan)->sum(fn($q) => $q['sub_total'] ?? 0) : 0;
        }
    }

    public function render()
    {
        return view('livewire.datamaster.tariftindakan.form');
    }
}
