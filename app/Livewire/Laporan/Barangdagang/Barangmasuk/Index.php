<?php

namespace App\Livewire\Laporan\Barangdagang\Barangmasuk;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\StokMasuk;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $jenis, $persediaan;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-d');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
        $this->jenis = $this->jenis ?: 'pertransaksi';
        $this->persediaan = $this->persediaan ?: '';
    }

    public function print()
    {
        $this->getData();
        $cetak = view('livewire.laporan.barangdagang.barangmasuk.cetak', [
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
            case 'pertransaksi':
                return StokMasuk::with(['barang', 'pengadaanPemesanan.pengadaanPemesananDetail', 'pengadaanPemesanan.kodeAkun', 'barangSatuan.satuanKonversi', 'pengadaanPemesanan.supplier', 'pengguna.kepegawaianPegawai'])
                    ->when($this->persediaan, fn($q) => $q->whereHas('barang', fn($q) => $q->where('persediaan', $this->persediaan)))
                    ->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2])
                    ->orderBy('tanggal', 'desc')
                    ->get()->map(function ($q) {
                        return [
                            'barang_id' => $q->barang->nama . $q->barang->id . $q->tanggal,
                            'tanggal' => $q->tanggal,
                            'barang' => $q->barang->nama,
                            'satuan' => $q->barangSatuan->nama . ' ' . $q->barangSatuan->konversi_satuan,
                            'no_batch' => $q->no_batch,
                            'metode_bayar' => $q->pengadaanPemesanan ? ($q->pengadaanPemesanan->pembayaran ? ($q->pengadaanPemesanan->pembayaran == 'Lunas' ? $q->pengadaanPemesanan->kodeAkun->nama : 'Jatuh Tempo') : '<span class="text-danger">Koreksi</span>') : '',
                            'tanggal_kedaluarsa' => $q->tanggal_kedaluarsa,
                            'harga_beli' => $q->harga_beli,
                            'qty' => $q->qty,
                            'total' => $q->qty * $q->harga_beli,
                            'supplier' => $q->pengadaanPemesanan ? $q->pengadaanPemesanan->supplier?->nama : '',
                            'uraian' => $q->pengadaanPemesanan ? $q->pengadaanPemesanan->uraian : '',
                            'operator' => $q->pengguna->nama,
                        ];
                    })->sortBy('barang_id')->groupBy('barang_id')->toArray();
                break;
            case 'perbarang':
                return StokMasuk::with(['barang', 'barangSatuan.satuanKonversi'])
                    ->when($this->persediaan, fn($q) => $q->whereHas('barang', fn($q) => $q->where('persediaan', $this->persediaan)))
                    ->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2])
                    ->get()->map(function ($q) {
                        return [
                            'nama' => $q->barang->nama,
                            'barang_id' => $q->barang->nama . $q->barang->id . $q->barang_satuan_id,
                            'satuan' => $q->barangSatuan->nama . ' ' . $q->barangSatuan->konversi_satuan,
                            'qty' => $q->qty,
                        ];
                    })->sortBy('barang_id')->groupBy('barang_id')->toArray();
                break;
        }
    }

    public function render()
    {
        return view('livewire.laporan.barangdagang.barangmasuk.index', [
            'data' => ($this->getData()),
        ]);
    }
}
