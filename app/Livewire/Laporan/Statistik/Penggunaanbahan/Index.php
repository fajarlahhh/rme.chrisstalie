<?php

namespace App\Livewire\Laporan\Statistik\Penggunaanbahan;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\TindakanBahan;
use App\Models\TindakanAlatBarang;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
    }


    public function getData()
    {
        return TindakanAlatBarang::with('tindakan.registrasi', 'barang')->whereNotNull('barang_id')->whereHas('tindakan', fn($q) => $q->whereBetween('created_at', [$this->tanggal1 . ' 00:00:00', $this->tanggal2 . ' 23:59:59']))->get()->groupBy('barang_id')->map(function ($q) {
            return [
                'barang_id' => $q->first()->barang_id,
                'nama' => $q->first()->barang->nama,
                'biaya' => $q->sum(fn($q) => $q->biaya * $q->qty),
                'qty' => $q->sum('qty'),
            ];
        })->sortByDesc('qty')->values()->toArray();
    }

    public function print()
    {
        $cetak = view('livewire.laporan.statistik.penggunaanbahan.cetak', [
            'cetak' => false,
            'tanggal1' => $this->tanggal1,
            'tanggal2' => $this->tanggal2,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function render()
    {
        return view('livewire.laporan.statistik.penggunaanbahan.index', [
            'data' => $this->getData(),
        ]);
    }
}
