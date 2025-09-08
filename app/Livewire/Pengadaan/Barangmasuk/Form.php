<?php

namespace App\Livewire\Pengadaan\Barangmasuk;

use App\Models\Stok;
use Livewire\Component;
use App\Models\Pembelian;
use App\Models\StokMasuk;
use Illuminate\Support\Str;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    public $data, $dataPembelian = [], $barang = [];
    public $pembelian_id;

    public function updatedPembelianId()
    {
        $this->barang = [];
        $stokMasuk = StokMasuk::where('pembelian_id', $this->pembelian_id)->get()->map(fn($q) => [
            'id' => $q->barang_id,
            'qty_masuk' => $q->qty,
        ]);
        $barang = PembelianDetail::where('pembelian_id', $this->pembelian_id)->with('barang')->get()->map(fn($q) => [
            'id' => $q->barang_id,
            'nama' => $q->barang->nama,
            'barang_satuan_id' => $q->barang_satuan_id,
            'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            'satuan' => $q->barangSatuan?->nama . ' (' . $q->barangSatuan?->konversi_satuan . ')',
            'qty' => $q->qty - ($stokMasuk->where('id', $q->barang_id)->first()['qty_masuk'] ?? 0),
            'qty_masuk' => null,
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
            ->with('supplier')->get()->toArray();;
    }

    public function submit()
    {
        $this->validate([
            'pembelian_id' => 'required',
            'barang' => 'required|array',
        ]);

        DB::transaction(function () {
            $stokMasuk = [];
            $stok = [];
            foreach ($this->barang as $key => $value) {
                $id = Str::uuid();
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            StokMasuk::insert($stokMasuk);
            foreach (array_chunk($stok, 1000) as $chunk) {
                Stok::insert($chunk);
            }
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/pengadaan/barangmasuk/form');
    }

    public function render()
    {
        return view('livewire.pengadaan.barangmasuk.form');
    }
}
