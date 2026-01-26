<?php

namespace App\Livewire\Laporan\Kepegawaian\KepegawaianAbsensi;

use Livewire\Component;
use App\Models\KepegawaianPegawai;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $jenis = 'Rekap', $kepegawaian_pegawai_id;
    public $dataPegawai = [];

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-t');
        $this->dataPegawai = KepegawaianPegawai::orderBy('nama')->get()->toArray();
        $this->jenis = $this->jenis ?: 'Rekap';
        $this->kepegawaian_pegawai_id = $this->kepegawaian_pegawai_id ?: null;
    }

    public function updatedJenis()
    {
        $this->kepegawaian_pegawai_id = null;
    }

    public function print()
    {
        $cetak = view('livewire.laporan.kepegawaian.kepegawaianAbsensi.cetak', [
            'cetak' => true,
            'tanggal1' => $this->tanggal1,
            'tanggal2' => $this->tanggal2,
            'data' => $this->getData(),
            'jenis' => $this->jenis,
            'kepegawaian_pegawai_id' => collect($this->dataPegawai)->where('id', $this->kepegawaian_pegawai_id)->first(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    private function getData()
    {
        return KepegawaianPegawai::with(['kepegawaianAbsensi' => function ($query) {
            $query->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2]);
        }])->when($this->jenis == 'Per KepegawaianPegawai', fn($q) => $q->where('id', $this->kepegawaian_pegawai_id))->get()->toArray();
    }

    public function render()
    {
        return view('livewire.laporan.kepegawaian.kepegawaianAbsensi.index', [
            'data' => $this->getData(),
        ]);
    }
}
