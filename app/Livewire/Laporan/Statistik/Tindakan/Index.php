<?php

namespace App\Livewire\Laporan\Statistik\Tindakan;

use App\Models\Tindakan;
use Livewire\Component;
use Livewire\Attributes\Url;

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
        return Tindakan::with('tarifTindakan')->whereBetween('created_at', [$this->tanggal1 . ' 00:00:00', $this->tanggal2 . ' 23:59:59'])->get()->groupBy('tarif_tindakan_id')->map(function ($q) {
            return [
                'tarif_tindakan_id' => $q->first()->tarif_tindakan_id,
                'nama' => $q->first()->tarifTindakan->nama,
                'biaya' => $q->sum(fn($q) => $q->biaya * $q->qty),
                'qty' => $q->sum('qty'),
            ];
        })->sortByDesc('qty')->values()->toArray();  
    }

    public function print()
    {
        $cetak = view('livewire.laporan.statistik.tindakan.cetak', [
            'cetak' => false,
            'tanggal1' => $this->tanggal1,
            'tanggal2' => $this->tanggal2,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function render()
    {
        return view('livewire.laporan.statistik.tindakan.index', [
            'data' => $this->getData(),
        ]);
    }
}
