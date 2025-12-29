<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\KodeAkunNeraca;

class LaporanbukubesarExport implements FromView
{
    use Exportable;
    public $data, $bulan, $kodeAkunId, $dataKodeAkun;

    public function __construct($data, $bulan, $kodeAkunId, $dataKodeAkun)
    {
        $this->data = $data;
        $this->bulan = $bulan;
        $this->kodeAkunId = $kodeAkunId;
        $this->dataKodeAkun = $dataKodeAkun;
    }

    public function view(): View
    {
        //
        return view('livewire.laporan.keuanganbulanan.bukubesar.cetak', [
            'cetak' => true,
            'data' => $this->data,
            'saldo' => $this->kodeAkunId ?
                (collect($this->dataKodeAkun)->where('id', $this->kodeAkunId)->first()['kategori'] == 'Aktiva' ?
                    KodeAkunNeraca::where('kode_akun_id', $this->kodeAkunId)->where('periode', $this->bulan . '-01')->first()->debet ?? 0
                    : KodeAkunNeraca::where('kode_akun_id', $this->kodeAkunId)->where('periode', $this->bulan . '-01')->first()->kredit ?? 0)
                : 0,
            'bulan' => $this->bulan,
            'kodeAkunId' => $this->kodeAkunId,
        ]);
    }
}
