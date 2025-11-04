<?php

namespace App\Livewire\Pengadaanbrgdagang\Lainnya\Barangkhusus;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Class\BarangClass;
use Illuminate\Support\Facades\DB;
use App\Models\JurnalDetail;
use App\Models\StokMasuk;
use App\Models\Stok;
use App\Models\Jurnal;
use Illuminate\Support\Str;
use App\Traits\CustomValidationTrait;

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
            $data = new Pembelian();
            $data->tanggal = $this->tanggal;
            $data->jatuh_tempo = $this->pembayaran == "Jatuh Tempo" ? $this->jatuh_tempo : null;
            $data->pembayaran = $this->pembayaran == "Jatuh Tempo" ? $this->pembayaran : "Lunas";
            $data->kode_akun_id = $this->pembayaran == "Jatuh Tempo" ? '21000' : $this->pembayaran;
            $data->uraian = $this->uraian;
            $data->supplier_id = $this->supplier_id != '' ? $this->supplier_id : null;
            $data->permintaan_pembelian_id = null;
            $data->ppn = $this->ppn;
            $data->diskon = $this->diskon;
            $data->jenis = 'Barang Khusus';
            $data->pengguna_id = auth()->id();
            $data->save();
            $data->pembelianDetail()->delete();
            $data->pembelianDetail()->insert(collect($this->barang)->map(function ($q) use ($data) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty' => $q['qty'],
                    'harga_beli' => $q['harga_beli'],
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                    'pembelian_id' => $data->id,
                ];
            })->toArray());
            
            $stokMasuk = [];
            $stok = [];

            $jurnal = [];
            $jurnalDetail = [];
            foreach (collect($this->barang)->map(function ($q) use ($data) {
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
                    'pembelian_id' => $data->id,
                ];
            })->toArray() as $key => $value) {
                $id = Str::uuid();
                $idJurnal = Str::uuid();
                if ($value['qty'] > 0) {
                    $stokMasuk[] = [
                        'id' => $id,
                        'qty' => $value['qty'],
                        'no_batch' => $value['no_batch'],
                        'tanggal_kedaluarsa' => $value['tanggal_kedaluarsa'],
                        'barang_id' => $value['barang_id'],
                        'pembelian_id' => $data->id,
                        'barang_satuan_id' => $value['barang_satuan_id'],
                        'rasio_dari_terkecil' => $value['rasio_dari_terkecil'],
                        'pengguna_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                for ($i = 0; $i < $value['rasio_dari_terkecil'] * $value['qty']; $i++) {
                    $stok[] = [
                        'id' => Str::uuid(),
                        'barang_id' => $value['barang_id'],
                        'no_batch' => $value['no_batch'],
                        'tanggal_kedaluarsa' => $value['tanggal_kedaluarsa'],
                        'stok_masuk_id' => $id,
                        'tanggal_masuk' => now(),
                        'harga_beli' => $value['harga_beli'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $jurnal[] = [
                    'id' => $idJurnal,
                    'jenis' => 'Stok Masuk Barang Khusus',
                    'tanggal' => now(),
                    'uraian' => 'Stok Masuk Barang Khusus ' . $value['nama'],
                    'referensi_id' => $id,
                    'pengguna_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $jurnalDetail = [
                    [
                        'jurnal_id' => $idJurnal,
                        'debet' => 0,
                        'kredit' => $value['harga_beli'] * $value['qty'],
                        'kode_akun_id' => $data->kode_akun_id,
                    ],
                    [
                        'jurnal_id' => $idJurnal,
                        'debet' => $value['harga_beli'] * $value['qty'],
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
        $this->redirect('pengadaanbrgdagang/lainnya/barangkhusus');
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.lainnya.barangkhusus.form');
    }
}
