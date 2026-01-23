<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\PemesananPengadaan;

use App\Models\Jurnal;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Supplier;
use App\Models\PemesananPengadaan;
use App\Class\BarangClass;
use App\Class\JurnalClass;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanPengadaan;
use App\Traits\CustomValidationTrait;
use App\Models\PermintaanPengadaanDetail;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataSupplier = [], $barang = [], $dataKodeAkun = [];
    public $tanggal, $uraian, $jatuh_tempo, $pembayaran = "Jatuh Tempo", $ppn, $diskon, $totalHargaBeli, $supplier_id;

    public function mount(PermintaanPengadaan $data)
    {
        $this->data = $data;
        $this->tanggal = $this->tanggal ?: date('Y-m-d');

        $this->dataSupplier = Supplier::whereNotNull('konsinyator')->orderBy('nama')->get()->toArray();
        $this->dataKodeAkun = KodeAkun::where('parent_id', '11100')->detail()->get()->toArray();
        $this->barang = $this->data->permintaanPengadaanDetail->map(fn($q) => [
            'id' => $q->barang_satuan_id,
            'barang_id' => $q->barang_id,
            'nama' => $q->barangSatuan->barang->nama,
            'satuan' => $q->barangSatuan->nama,
            'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            'qty' => $q->qty_disetujui,
            'qty_disetujui' => $q->qty_disetujui,
            'harga_beli' => 0,
        ])->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'tanggal' => 'required|date',
            'uraian' => 'required',
            'supplier_id' => 'required|integer|exists:supplier,id',
            'pembayaran' => 'required',
            'jatuh_tempo' => 'nullable|date',
            'ppn' => 'required|integer',
            'diskon' => 'nullable|integer',
            'uraian' => 'required',
            'barang' => 'required|array',
            'barang.*.id' => 'required|integer',
            'barang.*.qty' => [
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $matches = [];
                    if (preg_match('/^barang\.(\d+)\.qty$/', $attribute, $matches)) {
                        $index = (int)$matches[1];
                        if (isset($this->barang[$index]['qty_disetujui']) && $value > $this->barang[$index]['qty_disetujui']) {
                            $fail('Max ' . $this->barang[$index]['qty_disetujui'] . ' ' . ($this->barang[$index]['satuan'] ?? ''));
                        }
                    }
                }
            ],
            'barang.*.harga_beli' => 'required|integer',
        ]);

        if ($this->pembayaran == "Jatuh Tempo") {
            $this->validateWithCustomMessages([
                'jatuh_tempo' => 'required|date',
            ]);
        }

        DB::transaction(function () {
            $data = new PemesananPengadaan();
            $data->tanggal = $this->tanggal;
            $data->jatuh_tempo = $this->pembayaran == "Jatuh Tempo" ? $this->jatuh_tempo : null;
            $data->pembayaran = $this->pembayaran == "Jatuh Tempo" ? $this->pembayaran : "Lunas";
            $data->kode_akun_id = $this->pembayaran == "Jatuh Tempo" ? '21100' : $this->pembayaran;
            $data->uraian = $this->uraian;
            $data->supplier_id = $this->supplier_id;
            $data->permintaan_pengadaan_id = $this->data->id;
            $data->ppn = $this->ppn;
            $data->diskon = $this->diskon;
            $data->jenis = 'Barang Dagang';
            $data->pengguna_id = auth()->id();
            $data->save();
            $data->pemesananPengadaanDetail()->delete();
            $data->pemesananPengadaanDetail()->insert(collect($this->barang)->map(fn($q) => [
                'qty' => $q['qty'],
                'harga_beli' => $q['harga_beli'],
                'barang_id' => $q['barang_id'],
                'barang_satuan_id' => $q['id'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
                'harga_beli_terkecil' => $q['harga_beli'] / $q['rasio_dari_terkecil'],
                'pemesanan_pengadaan_id' => $data->id,
            ])->toArray());
            
            JurnalClass::insert(
                jenis: 'Pembelian',
                sub_jenis: 'Pembelian Barang Dagang',
                tanggal: $this->tanggal,
                uraian: $this->uraian,
                system: 1,
                pemesanan_pengadaan_id: $data->id,
                aset_id: null,
                stok_masuk_id: null,
                pembayaran_id: null,
                penggajian_id: null,
                pelunasan_pemesanan_pengadaan_id: null,
                stok_keluar_id: null,
                detail: [
                    [
                        'debet' => collect($this->barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']),
                        'kredit' => 0,
                        'kode_akun_id' => '12000'
                    ],
                    [
                        'debet' => 0,
                        'kredit' => $this->diskon,
                        'kode_akun_id' => '45000'
                    ],
                    [
                        'debet' => $this->ppn,
                        'kredit' => 0,
                        'kode_akun_id' => '11400'
                    ],
                    [
                        'debet' => 0,
                        'kredit' => collect($this->barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']) - $this->diskon + $this->ppn,
                        'kode_akun_id' => $data->kode_akun_id
                    ]
                ]
            );

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/manajemenstok/pengadaanbrgdagang/pemesanan_pengadaan');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.pemesanan_pengadaan.form');
    }
}
