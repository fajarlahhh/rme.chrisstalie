<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Stokmasuk;

use App\Models\Stok;
use App\Models\KeuanganJurnal;
use Livewire\Component;
use App\Models\PengadaanPemesanan;
use App\Models\StokMasuk;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Models\KeuanganJurnalDetail;
use App\Models\PembelianDetail;
use App\Models\PengadaanPemesananDetail;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataPembelian = [], $barang = [];
    public $pengadaan_pemesanan_id;


    public function updatedPengadaanPemesananId($value)
    {
        $this->barang = [];
        $stokMasuk = StokMasuk::where('pengadaan_pemesanan_id', $value)->get()->map(fn($q) => [
            'barang_id' => $q->barang_id,
            'qty' => $q->qty,
        ]);
        $barang = PengadaanPemesananDetail::where('pengadaan_pemesanan_id', $value)->with('barang')->get()->map(fn($q) => [
            'id' => $q->barang_id,
            'nama' => $q->barang->nama,
            'kode_akun_id' => $q->barang->kode_akun_id,
            'barang_satuan_id' => $q->barang_satuan_id,
            'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            'satuan' => $q->barangSatuan->nama,
            'qty' => $q->qty - ($stokMasuk->where('barang_id', ($q->barang_id))->sum('qty') ?? 0),
            'qty_masuk' => 0,
            'harga_beli' => $q->harga_beli,
            'harga_beli_terkecil' => $q->harga_beli_terkecil,
            'no_batch' => null,
            'tanggal_kedaluarsa' => null,
        ])->toArray();
        $this->barang = collect($barang)->filter(function ($q) {
            return $q['qty_masuk'] < $q['qty'];
        })->sortBy('barang_id')->values()->toArray();
    }

    public function mount()
    {
        $this->dataPembelian = PengadaanPemesanan::select(DB::raw('pengadaan_pemesanan.id id'), 'tanggal', 'supplier_id', 'uraian', 'nomor')
            ->leftJoin('pengadaan_pemesanan_detail', 'pengadaan_pemesanan.id', '=', 'pengadaan_pemesanan_detail.pengadaan_pemesanan_id')
            ->groupBy('pengadaan_pemesanan.id', 'tanggal', 'supplier_id', 'uraian')
            ->havingRaw('SUM(pengadaan_pemesanan_detail.qty) > (SELECT ifnull(SUM(stok_masuk.qty), 0) FROM stok_masuk WHERE pengadaan_pemesanan_id = pengadaan_pemesanan.id )')
            ->with('supplier')->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'pengadaan_pemesanan_id' => 'required',
            'barang' => 'required|array',
            'barang.*.qty_masuk' => [
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $matches = [];
                    if (preg_match('/^barang\.(\d+)\.qty_masuk$/', $attribute, $matches)) {
                        $index = (int)$matches[1];
                        if (isset($this->barang[$index]['qty']) && $value > $this->barang[$index]['qty']) {
                            $fail('Max ' . $this->barang[$index]['qty'] . ' ' . $this->barang[$index]['satuan']);
                        }
                    }
                }
            ],
            'barang.*.no_batch' => [
                function ($attribute, $value, $fail) {
                    $matches = [];
                    if (preg_match('/^barang\.(\d+)\.no_batch$/', $attribute, $matches)) {
                        $index = (int)$matches[1];
                        $qty_masuk = $this->barang[$index]['qty_masuk'] ?? 0;
                        if ($qty_masuk > 0) {
                            if (empty($value)) {
                                $fail('No. Batch wajib diisi');
                            }
                        }
                    }
                }
            ],
            'barang.*.tanggal_kedaluarsa' => [
                function ($attribute, $value, $fail) {
                    $matches = [];
                    if (preg_match('/^barang\.(\d+)\.tanggal_kedaluarsa$/', $attribute, $matches)) {
                        $index = (int)$matches[1];
                        $qty_masuk = $this->barang[$index]['qty_masuk'] ?? 0;
                        if ($qty_masuk > 0) {
                            if (empty($value)) {
                                $fail('Tanggal kedaluarsa wajib diisi');
                            }
                        }
                    }
                }
            ],
        ]);

        DB::transaction(function () {
            $stok = [];

            foreach ($this->barang as $key => $value) {
                if ($value['qty_masuk'] > 0) {
                    $stokMasuk = new StokMasuk();
                    $stokMasuk->tanggal = now();
                    $stokMasuk->qty = $value['qty_masuk'];
                    $stokMasuk->no_batch = $value['no_batch'];
                    $stokMasuk->tanggal_kedaluarsa = $value['tanggal_kedaluarsa'];
                    $stokMasuk->barang_id = $value['id'];
                    $stokMasuk->pengadaan_pemesanan_id = $this->pengadaan_pemesanan_id;
                    $stokMasuk->barang_satuan_id = $value['barang_satuan_id'];
                    $stokMasuk->rasio_dari_terkecil = $value['rasio_dari_terkecil'];
                    $stokMasuk->pengguna_id = auth()->id();
                    $stokMasuk->created_at = now();
                    $stokMasuk->updated_at = now();
                    $stokMasuk->save();

                    for ($i = 0; $i < $value['rasio_dari_terkecil'] * $value['qty_masuk']; $i++) {
                        $stok[] = [
                            'id' => Str::uuid(),
                            'barang_id' => $value['id'],
                            'no_batch' => $value['no_batch'],
                            'pengadaan_pemesanan_id' => $this->pengadaan_pemesanan_id,
                            'tanggal_kedaluarsa' => $value['tanggal_kedaluarsa'],
                            'stok_masuk_id' => $stokMasuk->id,
                            'tanggal_masuk' => now(),
                            'harga_beli' => $value['harga_beli_terkecil'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    JurnalkeuanganClass::insert(
                        jenis: 'Pembelian',
                        sub_jenis: 'Stok Masuk Barang Dagang',
                        tanggal: now(),
                        uraian: 'Stok Masuk Barang Dagang ' . $value['nama'],
                        system: 1,
                        foreign_key: 'pengadaan_pemesanan_id',
                        foreign_id: $this->pengadaan_pemesanan_id,
                        detail: [
                            [
                                'kode_akun_id' => $value['kode_akun_id'],
                                'debet' => $value['harga_beli'] * $value['qty_masuk'],
                                'kredit' => 0,
                            ],
                            [
                                'kode_akun_id' => '12000',
                                'debet' => 0,
                                'kredit' => $value['harga_beli'] * $value['qty_masuk'],
                            ]
                        ]
                    );
                }
            }
            foreach (array_chunk($stok, 1000) as $chunk) {
                Stok::insert($chunk);
            }
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/manajemenstok/pengadaanbrgdagang/stokmasuk');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.stokmasuk.form');
    }
}
