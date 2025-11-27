<?php

namespace App\Livewire\Laporan\Absensipegawai;

use Livewire\Component;
use App\Models\Pegawai;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $jenis = 'Rekap', $pegawai_id;
    public $dataPegawai = [];

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-t');
        $this->dataPegawai = Pegawai::orderBy('nama')->get()->toArray();
        $this->jenis = $this->jenis ?: 'Rekap';
        $this->pegawai_id = $this->pegawai_id ?: null;
    }

    public function updatedJenis()
    {
        $this->pegawai_id = null;
    }

    public function print()
    {
        $cetak = view('livewire.laporan.absensipegawai.cetak', [
            'cetak' => true,
            'tanggal1' => $this->tanggal1,
            'tanggal2' => $this->tanggal2,
            'data' => $this->getData(),
            'jenis' => $this->jenis,
            'pegawai_id' => collect($this->dataPegawai)->where('id', $this->pegawai_id)->first(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    private function getData()
    {
        return Pegawai::with(['absensi' => function ($query) {
            $query->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2]);
        }])->when($this->jenis == 'Per Pegawai', fn($q) => $q->where('id', $this->pegawai_id))->get()->toArray();
    }

    public function render()
    {
        return view('livewire.laporan.absensipegawai.index', [
            'data' => $this->getData(),
        ]);
    }
}
