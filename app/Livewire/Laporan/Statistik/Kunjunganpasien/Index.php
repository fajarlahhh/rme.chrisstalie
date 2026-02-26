<?php

namespace App\Livewire\Laporan\Statistik\Kunjunganpasien;

use Livewire\Component;
use App\Models\Pembayaran;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $sort = 'qty';

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
    }

    public function getData()
    {
        return Pembayaran::with('pasien')->whereNotNull('pasien_id')->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2])->get()->groupBy('pasien_id')->map(function ($q) {
            return [
                'pasien_id' => $q->first()->pasien_id,
                'id' => $q->first()->pasien->id,
                'alamat' => $q->first()->pasien->alamat,
                'jenis_kelamin' => $q->first()->pasien->jenis_kelamin,
                'nama' => $q->first()->pasien->nama,
                'biaya' => $q->sum(fn($q) => $q->total_tagihan),
                'qty' => $q->count(),
            ];
        })->sortByDesc($this->sort)->values()->toArray();
    }

    public function print()
    {
        $cetak = view('livewire.laporan.statistik.kunjunganpasien.cetak', [
            'cetak' => false,
            'tanggal1' => $this->tanggal1,
            'tanggal2' => $this->tanggal2,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function render()
    {
        return view('livewire.laporan.statistik.kunjunganpasien.index', [
            'data' => $this->getData(),
        ]);
    }
}
