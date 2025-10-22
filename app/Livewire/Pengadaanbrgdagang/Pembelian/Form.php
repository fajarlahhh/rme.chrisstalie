<?php

namespace App\Livewire\Pengadaanbrgdagang\Pembelian;

use App\Models\Jurnal;
use Livewire\Component;
use App\Models\Supplier;
use App\Models\Pembelian;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanPembelian;
use App\Models\PermintaanPembelianDetail;
use App\Models\KodeAkun;

class Form extends Component
{
    public $data, $previous, $dataSupplier = [], $dataPermintaanPembelian = [], $barang = [], $dataKodeAkun = [];
    public $tanggal, $uraian, $jatuh_tempo, $pembayaran = "Jatuh Tempo", $ppn, $diskon, $totalHargaBeli, $supplier_id;

    public function updatedBarang()
    {
        $this->totalHargaBeli = collect($this->barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']);
    }

    public function mount(PermintaanPembelian $data)
    {
        $this->data = $data;
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
        $this->previous = url()->previous();
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
        $this->validate([
            'tanggal' => 'required',
            'uraian' => 'required',
            'permintaan_pembelian_id' => [
                'required',
                Rule::exists('permintaan_pembelian', 'id')->whereNotIn('id', Pembelian::pluck('permintaan_pembelian_id')->filter()->all()),
            ],
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
            $data->uraian = $this->uraian;
            $data->supplier_id = $this->supplier_id;
            $data->permintaan_pembelian_id = $this->permintaan_pembelian_id;
            $data->ppn = $this->ppn;
            $data->diskon = $this->diskon;
            $data->pengguna_id = auth()->id();
            $data->save();
            $data->pembelianDetail()->delete();
            $data->pembelianDetail()->insert(collect($this->barang)->map(fn($q) => [
                'qty' => $q['qty'],
                'harga_beli' => $q['harga_beli'],
                'barang_satuan_id' => $q['barang_satuan_id'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
                'barang_id' => $q['id'],
                'pembelian_id' => $data->id,
            ])->toArray());


            // $id = Str::uuid();

            // $jurnal = new Jurnal();
            // $jurnal->id = $id;
            // $jurnal->jenis = 'Pembelian Barang Dagang';
            // $jurnal->tanggal = $this->tanggal;
            // $jurnal->uraian = $this->uraian;
            // $jurnal->referensi_id = $data->id;
            // $jurnal->pengguna_id = auth()->id();
            // $jurnal->save();

            // $jurnal->jurnalDetail()->delete();
            // $jurnal->jurnalDetail()->insert(collect($this->barang)->map(fn($q, $index) => [
            //     'jurnal_id' => $id,
            //     'debet' => 0,
            //     'kredit' => collect($this->barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']),
            //     'kode_akun_id' => $this->pembayaran == "Jatuh Tempo" ? '21110' : $this->pembayaran
            // ])->toArray());
            // $jurnal->jurnalDetail()->insert(collect($this->barang)->map(fn($q, $index) => [
            //     'jurnal_id' => $id,
            //     'debet' => collect($this->barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']),
            //     'kredit' => 0,
            //     'kode_akun_id' => '11420'
            // ])->toArray());
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.pembelian.form');
    }
}
