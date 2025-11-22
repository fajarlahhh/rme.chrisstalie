<?php

namespace App\Livewire\Pengadaanbrgdagang\Lainnya\Alatdanbahan;

use App\Models\Stok;
use Livewire\Component;
use App\Class\StokClass;
use App\Models\KodeAkun;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Models\StokMasuk;
use App\Class\BarangClass;
use App\Class\JurnalClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataBarang = [], $dataSupplier = [], $barang = [], $dataKodeAkun = [];
    public $tanggal, $uraian, $jatuh_tempo, $pembayaran = "Jatuh Tempo", $ppn, $diskon, $totalHargaBeli, $supplier_id;

    public function mount()
    {

        $this->tanggal = $this->tanggal ?: date('Y-m-d');
        $this->dataBarang = BarangClass::getBarangBySatuanUtama('Klinik', 0);
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
            'ppn' => 'required|integer',
            'diskon' => 'nullable|integer',
            'uraian' => 'required',
            'barang' => 'required|array',
            'barang.*.id' => 'required|integer',
            'barang.*.qty' => 'required|numeric',
            'barang.*.harga_beli' => 'required|integer',
            'barang.*.tanggal_kedaluarsa' => 'required|date',
        ]);

        DB::transaction(function () {
            $pembelian = new Pembelian();
            $pembelian->tanggal = $this->tanggal;
            $pembelian->jatuh_tempo = $this->pembayaran == "Jatuh Tempo" ? $this->jatuh_tempo : null;
            $pembelian->pembayaran = $this->pembayaran == "Jatuh Tempo" ? $this->pembayaran : "Lunas";
            $pembelian->kode_akun_id = $this->pembayaran == "Jatuh Tempo" ? '21000' : $this->pembayaran;
            $pembelian->uraian = $this->uraian;
            $pembelian->supplier_id = $this->supplier_id != '' ? $this->supplier_id : null;
            $pembelian->permintaan_pembelian_id = null;
            $pembelian->ppn = $this->ppn;
            $pembelian->diskon = $this->diskon;
            $pembelian->jenis = 'Alat dan Bahan';
            $pembelian->pengguna_id = auth()->id();
            $pembelian->save();
            $pembelian->pembelianDetail()->delete();
            $pembelian->pembelianDetail()->insert(collect($this->barang)->map(function ($q) use ($pembelian) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty' => $q['qty'],
                    'harga_beli' => $q['harga_beli'],
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                    'pembelian_id' => $pembelian->id,
                ];
            })->toArray());
            
            $stok = [];
            foreach (
                collect($this->barang)->map(function ($q) use ($pembelian) {
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
                        'pembelian_id' => $pembelian->id,
                    ];
                })->toArray() as $key => $value
            ) {
                if ($value['qty'] > 0) {                    
                    $stokMasuk = new StokMasuk();
                    $stokMasuk->qty = $value['qty'];
                    $stokMasuk->no_batch = $value['no_batch'];
                    $stokMasuk->tanggal_kedaluarsa = $value['tanggal_kedaluarsa'];
                    $stokMasuk->barang_id = $value['barang_id'];
                    $stokMasuk->pembelian_id = $value['pembelian_id'];
                    $stokMasuk->barang_satuan_id = $value['barang_satuan_id'];
                    $stokMasuk->rasio_dari_terkecil = $value['rasio_dari_terkecil'];
                    $stokMasuk->pengguna_id = auth()->id();
                    $stokMasuk->save();
                    
                    for ($i = 0; $i < $value['rasio_dari_terkecil'] * $value['qty']; $i++) {
                        $stok[] = [
                            'id' => Str::uuid(),
                            'pembelian_id' => $value['pembelian_id'],
                            'barang_id' => $value['barang_id'],
                            'no_batch' => $value['no_batch'],
                            'tanggal_kedaluarsa' => $value['tanggal_kedaluarsa'],
                            'stok_masuk_id' => $stokMasuk->id,
                            'tanggal_masuk' => now(),
                            'harga_beli' => $value['harga_beli'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }                    
                }
            }

            foreach (array_chunk($stok, 2000) as $chunk) {
                Stok::insert($chunk);
            }

            $detail = collect($this->barang)->map(function ($q) use ($pembelian) {
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
                'kode_akun_id' => $pembelian->kode_akun_id,
                'debet' => 0,
                'kredit' => collect($detail)->sum('debet'),
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
                jenis: 'Stok Masuk Alat dan Bahan',
                sub_jenis: 'Stok Masuk',
                tanggal: now(),
                uraian: 'Stok Masuk Alat dan Bahan ' . $pembelian->uraian,
                system: 1,
                pembelian_id: $pembelian->id,
                aset_id: null,
                stok_masuk_id: null,
                pembayaran_id: null,
                penggajian_id: null,
                detail: $detail
            );

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/pengadaanbrgdagang/lainnya/alatdanbahan');
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.lainnya.alatdanbahan.form');
    }
}
