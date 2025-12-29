<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class LaporanneracalajurExport implements FromView
{
    use Exportable;
    public $data, $bulan;

    public function __construct($data, $bulan)
    {
        $this->data = $data;
        $this->bulan = $bulan;
    }

    public function view(): View
    {
        //
        return view('livewire.laporan.keuanganbulanan.neracalajur.cetak', [
            'cetak' => true,
            'data' => $this->data,
            'bulan' => $this->bulan,
        ]);
    }
}
