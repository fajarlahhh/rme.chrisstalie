<?php

namespace App\Livewire\Laporan\Pengadaan;

use Livewire\Component;
use App\Models\Purchase;
use App\Livewire\Pengeluaran\Pengadaan;

class Index extends Component
{
    public $date1, $date2;

    public function mount()
    {
        $this->date1 = $this->date1 ?: date('Y-m-01');
        $this->date2 = $this->date2 ?: date('Y-m-d');
    }

    public function getData()
    {
        return Purchase::with(['purchaseDetail.goods', 'pengguna', 'stokMasuk', 'expenditure.pengguna'])->whereBetween('date', [$this->date1, $this->date2])->orderBy('created_at', 'desc')->get();
    }

    public function print()
    {
        $cetak = view('livewire.laporan.pengadaanbrgdagang.cetak', [
            'cetak' => true,
            'date1' => $this->date1,
            'date2' => $this->date2,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function render()
    {
        return view('livewire.laporan.pengadaanbrgdagang.index', [
            'data' => $this->getData()
        ]);
    }
}
