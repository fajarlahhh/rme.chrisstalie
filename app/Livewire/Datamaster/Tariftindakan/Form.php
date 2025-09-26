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

    public function tambahAlatBarang($jenis)
    {
        array_push($this->alatBarang, [
            'barang_id' => null,
            'aset_id' => null,
            'jenis' => $jenis,
            'barang_satuan_id' => null,
            'barangSatuan' => [],
            'qty' => 0,
            'biaya' => 0,
            'rasio_dari_terkecil' => 0,
        ]);
    }

    public function updatedAlatBarang($value, $key)
    {
        $index = explode('.', $key);
        if ($value) {
            if ($index[1] == 'barang_id') {
                $barang = collect($this->dataBarang)->where('id', $value)->first();
                $barangSatuan = collect($barang['barangSatuan']);
                $this->alatBarang[$index[0]]['barang_id'] = $barang['id'] ?? null;
                $this->alatBarang[$index[0]]['barang_satuan_id'] = null;
                $this->alatBarang[$index[0]]['barangSatuan'] = $barangSatuan->toArray();
                $this->alatBarang[$index[0]]['qty'] = $this->alatBarang[$index[0]]['qty'] ?? 0;
                $this->alatBarang[$index[0]]['rasio_dari_terkecil'] = null;
                $this->alatBarang[$index[0]]['biaya'] = 0;
            }
            if ($index[1] == 'aset_id') {
                $alat = collect($this->dataAset)->where('id', $value)->first();
                $this->alatBarang[$index[0]]['aset_id'] = $alat['id'] ?? null;
                $this->alatBarang[$index[0]]['biaya'] = $alat['metode_penyusutan'] == 'Satuan Hasil Produksi' ? $alat['harga_perolehan'] / $alat['masa_manfaat'] : 0;
            }

            if ($index[1] == 'barang_satuan_id') {
                if ($this->alatBarang[$index[0]]['jenis'] == 'Barang') {
                    $barang = collect($this->dataBarang)->where('id', $this->alatBarang[$index[0]]['id'])->first();
                    $barangSatuan = collect($barang['barangSatuan']);
                    $selectedSatuan = $barangSatuan->where('id', $this->alatBarang[$index[0]]['barang_satuan_id'])->first();
                    $this->alatBarang[$index[0]]['barang_satuan_id'] = $this->alatBarang[$index[0]]['barang_satuan_id'];
                    $this->alatBarang[$index[0]]['rasio_dari_terkecil'] = $selectedSatuan['rasio_dari_terkecil'];
                    $this->alatBarang[$index[0]]['biaya'] = $selectedSatuan['harga_jual'] ?? 0;
                }
            }
        } else {
            if ($this->alatBarang[$index[0]]['jenis'] == 'Barang') {
                $this->alatBarang[$index[0]]['barang_id'] = null;
                $this->alatBarang[$index[0]]['aset_id'] = null;
                $this->alatBarang[$index[0]]['barang_satuan_id'] = null;
                $this->alatBarang[$index[0]]['barangSatuan'] = [];
                $this->alatBarang[$index[0]]['qty'] = 0;
                $this->alatBarang[$index[0]]['rasio_dari_terkecil'] = null;
                $this->alatBarang[$index[0]]['biaya'] = 0;
            }
        }
        if ($this->alatBarang[$index[0]]['jenis'] == 'Barang') {
            $harga = (int) ($this->alatBarang[$index[0]]['biaya'] ?? 0);
            $qty = (int) ($this->alatBarang[$index[0]]['qty'] ?? 0);
            $this->alatBarang[$index[0]]['sub_total'] = $harga * $qty;
            $this->biaya_bahan = collect($this->alatBarang)->where('jenis', 'Barang')->sum(fn($q) => $q['sub_total'] ?? 0);
        }
        if ($this->alatBarang[$index[0]]['jenis'] == 'Alat') {
            $harga = (int) ($this->alatBarang[$index[0]]['biaya'] ?? 0);
            $qty = (int) ($this->alatBarang[$index[0]]['qty'] ?? 0);
            $this->alatBarang[$index[0]]['sub_total'] = $harga * $qty;
            $this->biaya_alat = collect($this->alatBarang)->where('jenis', 'Alat')->sum(fn($q) => $q['sub_total'] ?? 0);
        }
    }

    public function hapusAlatBarang($key)
    {
        unset($this->alatBarang[$key]);
        $this->alatBarang = array_merge($this->alatBarang);
        $this->biaya_bahan = collect($this->alatBarang)->where('jenis', 'Barang')->sum(fn($q) => $q['sub_total'] ?? 0);
        $this->biaya_alat = collect($this->alatBarang)->where('jenis', 'Alat')->sum(fn($q) => $q['sub_total'] ?? 0);
    }

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
            $this->data->icd_9_cm = $this->icd_9_cm;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->nama = $this->nama;
            $this->data->biaya_jasa_dokter = $this->biaya_jasa_dokter;
            $this->data->biaya_jasa_perawat = $this->biaya_jasa_perawat;
            $this->data->tarif = $this->tarif;

            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->tarifTindakanAlatBarang()->delete();
            $this->data->tarifTindakanAlatBarang()->insert(collect($this->alatBarang)->map(fn($q) => [
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
