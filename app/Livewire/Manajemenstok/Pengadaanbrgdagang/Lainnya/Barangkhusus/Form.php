<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Lainnya\Barangkhusus;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Supplier;
use App\Models\PemesananPengadaan;
use App\Class\BarangClass;
use App\Class\JurnalClass;
use App\Models\StokMasuk;
use App\Models\Stok;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use Illuminate\Support\Str;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataBarang = [], $dataSupplier = [], $barang = [], $dataKodeAkun = [];
    public $tanggal, $uraian, $jatuh_tempo, $pembayaran = "Jatuh Tempo", $ppn, $diskon, $totalHargaBeli, $supplier_id;

    public function mount()
    {

        $this->tanggal = $this->tanggal ?: date('Y-m-d');
        $this->dataBarang = BarangClass::getBarangBySatuanUtama('Apotek', 1);
        $this->dataSupplier = Supplier::whereNotNull('konsinyator')->orderBy('nama')->get()->toArray();
        $this->dataKodeAkun = KodeAkun::where('parent_id', '11100')->detail()->get()->toArray();
    }
    public function submit()
    {
        
        $this->validateWithCustomMessages([
            'tanggal' => 'required',
            'uraian' => 'required',
            'pembayaran' => 'required',
            'jatuh_tempo' => 'nullable|date',
            'ppn' => 'required|numeric',
            'diskon' => 'nullable|numeric',
            'uraian' => 'required',
            'barang' => 'required|array',
            'barang.*.id' => 'required|numeric',
            'barang.*.qty' => 'required|numeric',
            'barang.*.harga_beli' => 'required|numeric',
            'barang.*.tanggal_kedaluarsa' => 'required|date',
        ]);

        DB::transaction(function () {
            $pemesananPengadaan = new PemesananPengadaan();
            $pemesananPengadaan->tanggal = $this->tanggal;
            $pemesananPengadaan->jatuh_tempo = $this->pembayaran == "Jatuh Tempo" ? $this->jatuh_tempo : null;
            $pemesananPengadaan->pembayaran = $this->pembayaran == "Jatuh Tempo" ? $this->pembayaran : "Lunas";
            $pemesananPengadaan->kode_akun_id = $this->pembayaran == "Jatuh Tempo" ? '21100' : $this->pembayaran;
            $pemesananPengadaan->uraian = $this->uraian;
            $pemesananPengadaan->supplier_id = $this->supplier_id != '' ? $this->supplier_id : null;
            $pemesananPengadaan->permintaan_pengadaan_id = null;
            $pemesananPengadaan->ppn = $this->ppn;
            $pemesananPengadaan->diskon = $this->diskon;
            $pemesananPengadaan->jenis = 'Barang Khusus';
            $pemesananPengadaan->pengguna_id = auth()->id();
            $pemesananPengadaan->save();
            $pemesananPengadaan->pemesananPengadaanDetail()->delete();
            $pemesananPengadaan->pemesananPengadaanDetail()->insert(collect($this->barang)->map(function ($q) use ($pemesananPengadaan) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty' => $q['qty'],
                    'harga_beli' => $q['harga_beli'],
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                    'barang_id' => $brg['barang_id'],
                    'harga_beli_terkecil' => $q['harga_beli'] / $brg['rasio_dari_terkecil'],
                    'pemesanan_pengadaan_id' => $pemesananPengadaan->id,
                ];
            })->toArray());

            $stok = [];
            $stokMasuk = [];

            foreach (
                collect($this->barang)->map(function ($q) use ($pemesananPengadaan) {
                    $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                    return [
                        'barang_id' => $brg['barang_id'],
                        'nama' => $brg['nama'],
                        'kode_akun_id' => $brg['kode_akun_id'],
                        'qty' => $q['qty'],
                        'harga_beli' => $q['harga_beli'],
                        'no_batch' => $q['no_batch'],
                        'tanggal_kedaluarsa' => $q['tanggal_kedaluarsa'],
                        'barang_satuan_id' => $q['id'],
                        'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                        'pemesanan_pengadaan_id' => $pemesananPengadaan->id,
                    ];
                })->toArray() as $key => $value
            ) {
                if ($value['qty'] > 0) {
                    $stokMasuk = new StokMasuk();
                    $stokMasuk->tanggal = $this->tanggal;
                    $stokMasuk->qty = $value['qty'];
                    $stokMasuk->no_batch = $value['no_batch'];
                    $stokMasuk->tanggal_kedaluarsa = $value['tanggal_kedaluarsa'];
                    $stokMasuk->barang_id = $value['barang_id'];
                    $stokMasuk->pemesanan_pengadaan_id = $value['pemesanan_pengadaan_id'];
                    $stokMasuk->barang_satuan_id = $value['barang_satuan_id'];
                    $stokMasuk->rasio_dari_terkecil = $value['rasio_dari_terkecil'];
                    $stokMasuk->pengguna_id = auth()->id();
                    $stokMasuk->save();

                    for ($i = 0; $i < $value['rasio_dari_terkecil'] * $value['qty']; $i++) {
                        $stok[] = [
                            'id' => $stokMasuk->id . '-' . $value['barang_id'] . '-' . $i,
                            'pemesanan_pengadaan_id' => $value['pemesanan_pengadaan_id'],
                            'barang_id' => $value['barang_id'],
                            'no_batch' => $value['no_batch'],
                            'tanggal_kedaluarsa' => $value['tanggal_kedaluarsa'],
                            'stok_masuk_id' => $stokMasuk->id,
                            'tanggal_masuk' => now()->toDateTimeString(), // Convert ke string biar hemat memori
                            'harga_beli' => $value['harga_beli'] / $value['rasio_dari_terkecil'],
                            'created_at' => now()->toDateTimeString(),
                            'updated_at' => now()->toDateTimeString(),
                        ];

                        if (count($stok) >= 2000) {
                            Stok::insert($stok);
                            $stok = [];
                        }
                    }
                }
            }

            if (!empty($stok)) {
                Stok::insert($stok);
            }

            $detail = collect($this->barang)->map(function ($q) use ($pemesananPengadaan) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'kode_akun_id' => $brg['kode_akun_id'],
                    'debet' => $q['harga_beli'] * $q['qty'],
                    'kredit' => 0,
                ];
            })->groupBy('kode_akun_id')->map(function ($q) {
                return [
                    'kode_akun_id' => $q->first()['kode_akun_id'],
                    'debet' => $q->sum(fn($q) => $q['debet']),
                    'kredit' => $q->sum(fn($q) => $q['kredit']),
                ];
            })->values()->toArray();
            $detail[] = [
                'kode_akun_id' => $pemesananPengadaan->kode_akun_id,
                'debet' => 0,
                'kredit' => collect($detail)->sum('debet') - $this->diskon + $this->ppn,
            ];
            $detail[] = [
                'kode_akun_id' => '11400',
                'debet' => $this->ppn,
                'kredit' => 0,
            ];
            $detail[] = [
                'kode_akun_id' => '45000',
                'debet' => 0,
                'kredit' => $this->diskon,
            ];

            JurnalClass::insert(
                jenis: 'Pembelian',
                sub_jenis: 'Stok Masuk Barang Khusus',
                tanggal: now(),
                uraian: 'Stok Masuk Barang Khusus ' . $pemesananPengadaan->uraian,
                system: 1,
                pemesanan_pengadaan_id: $pemesananPengadaan->id,
                aset_id: null,
                stok_masuk_id: null,
                pembayaran_id: null,
                penggajian_id: null,
                pelunasan_pemesanan_pengadaan_id: null,
                stok_keluar_id: null,
                detail: $detail
            );

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/manajemenstok/pengadaanbrgdagang/lainnya/barangkhusus');
    }


    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.lainnya.barangkhusus.form');
    }
}
