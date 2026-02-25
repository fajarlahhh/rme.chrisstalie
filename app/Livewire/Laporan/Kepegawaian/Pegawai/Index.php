<?php

namespace App\Livewire\Laporan\Kepegawaian\Pegawai;

use Livewire\Component;
use App\Models\KepegawaianPegawai;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $status = 'Aktif';

    public function print()
    {
        $cetak = view('livewire.laporan.kepegawaian.pegawai.cetak', [
            'cetak' => true,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    private function getData()
    {
        return KepegawaianPegawai::when($this->status, function ($query) {
            $query->where('status', $this->status);
        })->orderBy('nama')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.laporan.kepegawaian.pegawai.index', [
            'data' => $this->getData(),
        ]);
    }
}
