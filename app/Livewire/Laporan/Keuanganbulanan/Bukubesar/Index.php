<?php

namespace App\Livewire\Laporan\Keuanganbulanan\Bukubesar;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\KodeAkunNeraca;
use Livewire\Attributes\Url;
use App\Models\MutasiKeuangan;
use App\Exports\LaporanbukubesarExport;

class Index extends Component
{
    #[Url]
    public $bulan, $kodeAkunId;

    public $dataKodeAkun = [];

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
        $this->dataKodeAkun = KodeAkun::where('detail', 1)->get()->toArray();
    }


    public function export()
    {
        return (new LaporanbukubesarExport($this->getData(), $this->bulan, $this->kodeAkunId, $this->dataKodeAkun))->download('bukubesar' . $this->bulan . '.xls');
    }

    public function getData()
    {
        $data = [];
        if ($this->kodeAkunId) {
            $data = MutasiKeuangan::orderBy('periode')->orderBy('id')->where('kode_akun_id', $this->kodeAkunId)->where("periode", 'like', $this->bulan . '%')->get();
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.laporan.keuanganbulanan.bukubesar.index', [
            'saldo' => $this->kodeAkunId ?
                (collect($this->dataKodeAkun)->where('id', $this->kodeAkunId)->first()['kategori'] == 'Aktiva' ?
                    KodeAkunNeraca::where('kode_akun_id', $this->kodeAkunId)->where('periode', $this->bulan . '-01')->first()->debet ?? 0
                    : KodeAkunNeraca::where('kode_akun_id', $this->kodeAkunId)->where('periode', $this->bulan . '-01')->first()->kredit ?? 0)
                : 0,
            'data' => ($this->getData())
        ]);
    }
}
