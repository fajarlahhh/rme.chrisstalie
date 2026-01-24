<?php

namespace App\Livewire\Manajemenstok\Opname\Penambahan;

use App\Models\Stok;
use App\Models\Barang;
use Livewire\Component;
use App\Models\StokMasuk;
use App\Class\BarangClass;
use App\Class\JurnalkeuanganClass;
use App\Models\BarangSatuan;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [], $barang, $dataStok = [], $barang_id, $dataBarangSatuan = [];

    public $satuan_id, $satuan, $tanggal, $qty_masuk, $catatan, $harga_beli, $tanggal_kedaluarsa, $no_batch;

    public function updatedBarangId($value)
    {
        $this->barang = collect($this->dataBarang)->firstWhere('id', $value);
        $this->dataBarangSatuan = BarangSatuan::with('satuanKonversi')->where('barang_id', $value)->orderBy('rasio_dari_terkecil', 'asc')->get()->map(fn($q) => [
            'id' => $q['id'],
            'nama' => $q['nama'],
            'konversi_satuan' => $q['konversi_satuan'],
            'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
        ])->toArray();
    }

    public function updatedSatuanId($value)
    {
        $this->satuan = collect($this->dataBarangSatuan)->firstWhere('id', $value);
    }

    public function mount()
    {
        $this->barang_id = '';
        $this->dataBarang = Barang::orderBy('nama')->get()->toArray();
    }


    public function submit()
    {
        $this->validateWithCustomMessages([
            'barang_id' => 'required',
            'qty_masuk' => 'required',
            'harga_beli' => 'required',
            'tanggal_kedaluarsa' => 'required',
            'satuan_id' => 'required',
            'no_batch' => 'required',
            'catatan' => 'required',
        ]);

        DB::transaction(function () {

            $stok = [];

            $data = new StokMasuk();
            $data->tanggal = date('Y-m-d');
            $data->qty = $this->qty_masuk;
            $data->catatan = $this->catatan;
            $data->no_batch = $this->no_batch;
            $data->tanggal_kedaluarsa = $this->tanggal_kedaluarsa;
            $data->barang_id = $this->barang_id;
            $data->pemesanan_pengadaan_id = null;
            $data->barang_satuan_id = $this->satuan_id;
            $data->rasio_dari_terkecil = $this->satuan['rasio_dari_terkecil'];
            $data->harga_beli = $this->harga_beli;
            $data->pengguna_id = auth()->id();
            $data->save();

            for ($i = 0; $i < $this->satuan['rasio_dari_terkecil'] * $this->qty_masuk; $i++) {
                $stok[] = [
                    'id' => $data->id . '-' . $this->barang_id . '-' . $i,
                    'pemesanan_pengadaan_id' => null,
                    'barang_id' => $this->barang_id,
                    'no_batch' => $this->no_batch,
                    'tanggal_kedaluarsa' => $this->tanggal_kedaluarsa,
                    'stok_masuk_id' => $data->id,
                    'tanggal_masuk' => now()->toDateTimeString(),
                    'harga_beli' => $this->harga_beli / $this->satuan['rasio_dari_terkecil'],
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];

                if (count($stok) >= 2000) {
                    Stok::insert($stok);
                    $stok = [];
                }
            }
            if (!empty($stok)) {
                Stok::insert($stok);
            }
            $this->jurnalKeuangan($data, $this->harga_beli / $this->satuan['rasio_dari_terkecil'] * $this->qty_masuk);

            session()->flash('success', 'Berhasil menyimpan data');
        });
        return $this->redirect('/manajemenstok/opname/penambahan');
    }

    private function jurnalKeuangan($koreksi, $hargaBeli)
    {
        $detail[] = [
            'kode_akun_id' => $this->barang['kode_akun_id'],
            'debet' => $hargaBeli,
            'kredit' => 0
        ];
        $detail[] = [
            'kode_akun_id' => $this->barang['kode_akun_modal_id'],
            'debet' => 0,
            'kredit' => $hargaBeli,
        ];

        JurnalkeuanganClass::insert(
            jenis: 'Koreksi',
            sub_jenis: 'Koreksi Penambaan Stok',
            tanggal: now(),
            uraian: 'Koreksi Stok Barang ' . $this->barang['nama'],
            system: 1,
            aset_id: null,
            pemesanan_pengadaan_id: null,
            stok_masuk_id: $koreksi->id,
            pembayaran_id: null,
            penggajian_id: null,
            pelunasan_pemesanan_pengadaan_id: null,
            stok_keluar_id: null,
            detail: $detail
        );
    }
    public function render()
    {
        return view('livewire.manajemenstok.opname.penambahan.form');
    }
}
