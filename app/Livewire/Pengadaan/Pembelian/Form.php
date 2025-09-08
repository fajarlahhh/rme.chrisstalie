<?php

namespace App\Livewire\Pengadaan\Pembelian;

use App\Models\Barang;
use Livewire\Component;
use App\Models\Supplier;
use App\Models\Pembelian;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanPembelian;
use App\Models\PermintaanPembelianDetail;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public $data, $previous, $dataSupplier = [], $dataPermintaanPembelian = [], $barang = [];
    public $tanggal, $uraian, $jatuh_tempo, $permintaan_pembelian_id, $pembayaran = "Jatuh Tempo", $ppn, $diskon, $totalHargaBeli, $supplier_id;

    public function updatedBarang()
    {
        $this->totalHargaBeli = collect($this->barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']);
    }

    public function updatedPermintaanPembelianId()
    {
        $this->barang = [];
        $this->barang = PermintaanPembelianDetail::where('permintaan_pembelian_id', $this->permintaan_pembelian_id)->with('barang', 'barangSatuan')->get()->map(fn($q) => [
            'id' => $q->barang_id,
            'nama' => $q->barang->nama,
            'satuan' => $q->barangSatuan?->nama . ' (' . $q->barangSatuan?->konversi_satuan . ')',
            'qty' => $q->qty_disetujui,
            'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            'barang_satuan_id' => $q->barang_satuan_id,
            'harga_beli' => 0,
        ])->toArray();
    }

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
        $this->previous = url()->previous();
        $this->dataSupplier = Supplier::whereNotNull('konsinyator')->orderBy('nama')->get()->toArray();
        $this->dataPermintaanPembelian = PermintaanPembelian::whereNotIn(
            'id',
            Pembelian::pluck('permintaan_pembelian_id')->filter()->all()
        )->whereHas('verifikasiDisetujui')->orderBy('created_at')->get()->toArray();
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
            $data->pembayaran = $this->pembayaran;
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

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function render()
    {
        return view('livewire.pengadaan.pembelian.form');
    }
}
