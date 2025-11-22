<?php

namespace App\Livewire\Pengadaanbrgdagang\Pembelian;

use App\Models\Jurnal;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Class\BarangClass;
use App\Class\JurnalClass;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanPembelian;
use App\Traits\CustomValidationTrait;
use App\Models\PermintaanPembelianDetail;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataSupplier = [], $barang = [], $dataKodeAkun = [];
    public $tanggal, $uraian, $jatuh_tempo, $pembayaran = "Jatuh Tempo", $ppn, $diskon, $totalHargaBeli, $supplier_id;

    public function mount(PermintaanPembelian $data)
    {
        $this->data = $data;
        $this->tanggal = $this->tanggal ?: date('Y-m-d');

        $this->dataSupplier = Supplier::whereNotNull('konsinyator')->orderBy('nama')->get()->toArray();
        $this->dataKodeAkun = KodeAkun::where('parent_id', '11100')->detail()->get()->toArray();
        $this->barang = $this->data->permintaanPembelianDetail->map(fn($q) => [
            'id' => $q->barang_satuan_id,
            'nama' => $q->barangSatuan->barang->nama,
            'satuan' => $q->barangSatuan->nama,
            'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            'qty' => $q->qty_disetujui,
            'harga_beli' => 0,
        ])->toArray();
    }

    public function submit()
    {

        $this->validateWithCustomMessages([
            'tanggal' => 'required',
            'uraian' => 'required',
            'supplier_id' => 'required|integer|exists:supplier,id',
            'pembayaran' => 'required',
            'jatuh_tempo' => 'nullable|date',
            'ppn' => 'required|integer',
            'diskon' => 'nullable|integer',
            'uraian' => 'required',
            'barang' => 'required|array',
            'barang.*.id' => 'required|integer',
            'barang.*.qty' => 'required|numeric',
            'barang.*.harga_beli' => 'required|integer',
        ]);

        DB::transaction(function () {
            $data = new Pembelian();
            $data->tanggal = $this->tanggal;
            $data->jatuh_tempo = $this->pembayaran == "Jatuh Tempo" ? $this->jatuh_tempo : null;
            $data->pembayaran = $this->pembayaran == "Jatuh Tempo" ? $this->pembayaran : "Lunas";
            $data->kode_akun_id = $this->pembayaran == "Jatuh Tempo" ? '21000' : $this->pembayaran;
            $data->uraian = $this->uraian;
            $data->supplier_id = $this->supplier_id;
            $data->permintaan_pembelian_id = $this->data->id;
            $data->ppn = $this->ppn;
            $data->diskon = $this->diskon;
            $data->jenis = 'Barang Dagang';
            $data->pengguna_id = auth()->id();
            $data->save();
            $data->pembelianDetail()->delete();
            $data->pembelianDetail()->insert(collect($this->barang)->map(fn($q) => [
                'qty' => $q['qty'],
                'harga_beli' => $q['harga_beli'],
                'barang_satuan_id' => $q['id'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
                'pembelian_id' => $data->id,
            ])->toArray());

            JurnalClass::pembelianPersediaan(
                jenis: 'Pembelian Barang Dagang',
                tanggal: $this->tanggal,
                uraian: $this->uraian,
                ppn: $this->ppn,
                diskon: $this->diskon,
                kode_akun_id: $data->kode_akun_id,
                referensi_id: $data->id,
                barang: collect($this->barang)->map(fn($q) => [
                    'kode_akun_id' => '11340',
                    'qty' => $q['qty'],
                    'harga_beli' => $q['harga_beli'],
                ])->toArray()
            );

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/pengadaanbrgdagang/pembelian');
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.pembelian.form');
    }
}
