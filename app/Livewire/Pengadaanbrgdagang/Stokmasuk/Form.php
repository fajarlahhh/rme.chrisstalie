<?php

namespace App\Livewire\Pengadaanbrgdagang\Stokmasuk;

use App\Models\Stok;
use App\Models\Jurnal;
use Livewire\Component;
use App\Models\Pembelian;
use App\Models\StokMasuk;
use Illuminate\Support\Str;
use App\Models\JurnalDetail;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataPembelian = [], $barang = [];
    public $pembelian_id;


    public function updatedPembelianId($value)
    {
        $this->barang = [];
        $stokMasuk = StokMasuk::where('pembelian_id', $value)->get()->map(fn($q) => [
            'id' => $q->barangSatuan->barang_id,
            'qty_masuk' => $q->qty,
        ]);
        $barang = PembelianDetail::where('pembelian_id', $value)->with('barang')->get()->map(fn($q) => [
            'id' => $q->barangSatuan->barang_id,
            'nama' => $q->barangSatuan->barang->nama,
            'kode_akun_id' => $q->barangSatuan->barang->kode_akun_id,
            'barang_satuan_id' => $q->barang_satuan_id,
            'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            'satuan' => $q->barangSatuan->nama . ($q->barangSatuan->konversi_satuan ? ' (' . $q->barangSatuan->konversi_satuan . ')' : ''),
            'qty' => $q->qty - ($stokMasuk->where('id', ($q->barangSatuan->barang_id))->first()['qty_masuk'] ?? 0),
            'qty_masuk' => 0,
            'harga_beli' => $q->harga_beli,
            'no_batch' => null,
            'tanggal_kedaluarsa' => null,
        ])->toArray();
        $this->barang = collect($barang)->filter(function ($q) {
            return $q['qty_masuk'] < $q['qty'];
        })->sortBy('barang_id')->values()->toArray();
    }

    public function mount()
    {
        $this->dataPembelian = Pembelian::select(DB::raw('pembelian.id id'), 'tanggal', 'supplier_id', 'uraian')
            ->leftJoin('pembelian_detail', 'pembelian.id', '=', 'pembelian_detail.pembelian_id')
            ->groupBy('pembelian.id', 'tanggal', 'supplier_id', 'uraian')
            ->havingRaw('SUM(pembelian_detail.qty) > (SELECT ifnull(SUM(stok_masuk.qty), 0) FROM stok_masuk WHERE pembelian_id = pembelian.id )')
            ->with('supplier')->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'pembelian_id' => 'required',
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
            $stokMasuk = [];
            $stok = [];

            $jurnal = [];
            $jurnalDetail = [];
            foreach ($this->barang as $key => $value) {
                $id = Str::uuid();
                $idJurnal = Str::uuid();
                if ($value['qty_masuk'] > 0) {
                    $stokMasuk[] = [
                        'id' => $id,
                        'qty' => $value['qty_masuk'],
                        'no_batch' => $value['no_batch'],
                        'tanggal_kedaluarsa' => $value['tanggal_kedaluarsa'],
                        'barang_id' => $value['id'],
                        'pembelian_id' => $this->pembelian_id,
                        'barang_satuan_id' => $value['barang_satuan_id'],
                        'rasio_dari_terkecil' => $value['rasio_dari_terkecil'],
                        'pengguna_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                for ($i = 0; $i < $value['rasio_dari_terkecil'] * $value['qty_masuk']; $i++) {
                    $stok[] = [
                        'id' => Str::uuid(),
                        'barang_id' => $value['id'],
                        'no_batch' => $value['no_batch'],
                        'tanggal_kedaluarsa' => $value['tanggal_kedaluarsa'],
                        'stok_masuk_id' => $id,
                        'tanggal_masuk' => now(),
                        'harga_beli' => $value['harga_beli'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $pembelian = Pembelian::find($this->pembelian_id);
                $jurnal[] = [
                    'id' => $idJurnal,
                    'jenis' => 'Stok Masuk Barang Dagang',
                    'tanggal' => now(),
                    'uraian' => 'Stok Masuk Barang Dagang ' . $value['nama'],
                    'referensi_id' => $id,
                    'pengguna_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $jurnalDetail = [
                    [
                        'jurnal_id' => $idJurnal,
                        'debet' => 0,
                        'kredit' => $value['harga_beli'] * $value['qty_masuk'],
                        'kode_akun_id' => $pembelian->kode_akun_id,
                    ],
                    [
                        'jurnal_id' => $idJurnal,
                        'debet' => $value['harga_beli'] * $value['qty_masuk'],
                        'kredit' => 0,
                        'kode_akun_id' => $value['kode_akun_id'],
                    ]
                ];
            }
            StokMasuk::insert($stokMasuk);
            foreach (array_chunk($stok, 1000) as $chunk) {
                Stok::insert($chunk);
            }
            Jurnal::insert($jurnal);
            foreach (array_chunk($jurnalDetail, 1000) as $chunk) {
                JurnalDetail::insert($chunk);
            }

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/pengadaanbrgdagang/stokmasuk');
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.stokmasuk.form');
    }
}
