<?php

namespace App\Livewire\Laporan\Stokbarang;

use App\Models\Barang;
use Livewire\Component;

class Index extends Component
{
    public $year, $month, $cari;

    public function print()
    {
        $cetak = view('livewire.laporan.stokbarang.cetak', [
            'cetak' => true,
            'month' => $this->month,
            'year' => $this->year,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function getData()
    {
        return Barang::where('nama', 'like', '%' . $this->cari . '%')->with('pengguna')
            ->with(['goodsBalance' => fn($q) => $q->where('period', 'like',  $this->year . '-' . $this->month . '%')])
            ->with(['stokMasuk' => fn($q) => $q->where('date', 'like',  $this->year . '-' . $this->month . '%')])
            ->with(['saleDetail' => fn($q) => $q->whereHas('sale', fn($r) => $r->where('date', 'like',  $this->year . '-' . $this->month . '%'))])
            ->get();
    }

    public function mount()
    {
        $this->year = $this->year ?: date('Y');
        $this->month = $this->month ?: date('m');
    }

    public function render()
    {
        return view('livewire.laporan.stokbarang.index', [
            'data' => $this->getData(),
        ]);
    }
}
