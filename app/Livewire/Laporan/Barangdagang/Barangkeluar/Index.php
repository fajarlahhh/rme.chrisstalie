<?php

namespace App\Livewire\Laporan\Barangdagang\Barangkeluar;

use App\Models\Stok;
use App\Models\Barang;
use Livewire\Component;
use App\Models\StokKeluar;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $jenis, $persediaan;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-d');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
        $this->jenis = $this->jenis ?: 'perbarang';
        $this->persediaan = $this->persediaan ?: '';
    }

    public function print()
    {
        $this->getData();
        $cetak = view('livewire.laporan.barangdagang.barangkeluar.cetak', [
            'cetak' => true,
            'tanggal1' => $this->tanggal1,
            'tanggal2' => $this->tanggal2,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function getData()
    {
        switch ($this->jenis) {
            case 'perhargajual':
                return StokKeluar::with(['barang', 'barangSatuan.satuanKonversi'])
                    ->when($this->persediaan, fn($q) => $q->whereHas('barang', fn($q) => $q->where('persediaan', $this->persediaan)))
                    ->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2])
                    ->get()
                    ->map(function ($q) {
                        return [
                            'tanggal' => $q->tanggal,
                            'nama' => $q->barang->nama,
                            'barang_id' => $q->barang->nama . $q->barang->id . $q->barang_satuan_id,
                            'satuan' => $q->barangSatuan->nama . ' ' . $q->barangSatuan->konversi_satuan,
                            'harga_jual' => $q->harga,
                            'qty' => $q->qty,
                        ];
                    })->sortBy('barang_id')->groupBy('barang_id')->toArray();
                break;
            case 'pertanggalkedaluarsa':
                return Stok::with(['barang.barangSatuanTerkecil.satuanKonversi'])
                    ->whereNotNull('stok_keluar_id')
                    ->whereBetween('tanggal_keluar', [$this->tanggal1, $this->tanggal2])
                    ->get()
                    ->map(function ($q) {
                        return [
                            'tanggal_kedaluarsa' => $q->tanggal_kedaluarsa,
                            'barang_id' => $q->barang->nama . $q->barang->id . $q->tanggal_kedaluarsa,
                            'nama' => $q->barang->nama,
                            'satuan' => $q->barang->barangSatuanTerkecil->nama . ' ' . $q->barang->barangSatuanTerkecil->konversi_satuan,
                            'qty' => 1,
                        ];
                    })->sortBy('barang_id')->groupBy('barang_id')->toArray();
                break;
            case 'perbarang':
                return Stok::with(['barang.barangSatuanUtama.satuanKonversi'])
                    ->when($this->persediaan, fn($q) => $q->whereHas('barang', fn($q) => $q->where('persediaan', $this->persediaan)))
                    ->whereNotNull('stok_keluar_id')
                    ->whereBetween('tanggal_keluar', [$this->tanggal1, $this->tanggal2])
                    ->get()
                    ->map(function ($q) {
                        return [
                            'nama' => $q->barang->nama,
                            'barang_id' => $q->barang->nama . $q->barang_id,
                            'satuan' => $q->barang->barangSatuanUtama->nama . ' ' . $q->barang->barangSatuanUtama->konversi_satuan,
                            'qty' => 1 / $q->barang->barangSatuanUtama->rasio_dari_terkecil,
                        ];
                    })
                    ->sortBy('barang_id')
                    ->groupBy('barang_id')
                    ->toArray();
                break;
        }
    }

    public function render()
    {
        return view('livewire.laporan.barangdagang.barangkeluar.index', [
            'data' => ($this->getData()),
        ]);
    }
}
