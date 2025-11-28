<?php

namespace App\Livewire\Laporan\Barangdagang\Barangmasuk;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\StokMasuk;

class Index extends Component
{
    #[Url]
    public $bulan, $jenis = 'pertransaksi';

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function print()
    {
        $this->getData();
        $cetak = view('livewire.laporan.barangdagang.barangmasuk.cetak', [
            'cetak' => true,
            'bulan' => $this->bulan,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function getData()
    {
        return StokMasuk::with(['barang', 'pembelian.pembelianDetail', 'barangSatuan', 'pembelian.supplier'])
            ->where('tanggal', 'like', $this->bulan . '%')
            ->orderBy('tanggal', 'desc')
            ->get()->map(function ($q) {
                return [
                    'tanggal' => $q->tanggal,
                    'barang' => $q->barang->nama,
                    'satuan' => $q->barangSatuan->nama,
                    'no_batch' => $q->no_batch,
                    'tanggal_kedaluarsa' => $q->tanggal_kedaluarsa,
                    'harga_beli' => $q->pembelian->pembelianDetail->where('barang_id', $q->barang_id)->first()->harga_beli,
                    'qty' => $q->qty,
                    'total' => $q->qty * $q->pembelian->pembelianDetail->where('barang_id', $q->barang_id)->first()->harga_beli,
                    'supplier' => $q->pembelian->supplier?->nama,
                    'uraian' => $q->pembelian->uraian,
                ];
            })->toArray();
    }

    public function render()
    {
        return view('livewire.laporan.barangdagang.barangmasuk.index', [
            'data' => ($this->getData()),
        ]);
    }
}
