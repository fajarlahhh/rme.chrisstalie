<?php

namespace App\Livewire\Laporan\Statistik\Penggunaanalat;

use App\Models\TindakanAlatBarang;
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
        return TindakanAlatBarang::with('tindakan.registrasi', 'alat')->whereNotNull('aset_id')->whereHas('tindakan', fn($q) => $q->whereBetween('created_at', [$this->tanggal1 . ' 00:00:00', $this->tanggal2 . ' 23:59:59']))->get()->groupBy('aset_id')->map(function ($q) {
            return [
                'aset_id' => $q->first()->aset_id,
                'nama' => $q->first()->alat->nama,
                'biaya' => $q->sum(fn($q) => $q->biaya * $q->qty),
                'qty' => $q->sum('qty'),
            ];
        })->sortByDesc('nama')->values()->toArray();
    }

    public function render()
    {
        return view('livewire.laporan.statistik.penggunaanalat.index', [
            'data' => $this->getData(),
        ]);
    }
}
