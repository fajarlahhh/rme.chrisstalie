<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class LaporanjasanpelayananExport implements FromView
{
    use Exportable;
    public $data, $tanggal1, $tanggal2, $jenis;

    public function __construct($data, $tanggal1, $tanggal2, $jenis)
    {
        $this->data = $data;
        $this->tanggal1 = $tanggal1;
        $this->tanggal2 = $tanggal2;
        $this->jenis = $jenis;
    }

    public function view(): View
    {
        //
        return view('livewire.laporan.jasa' . $this->jenis . '.cetak', [
            'cetak' => true,
            'data' => $this->data,
            'tanggal1' => $this->tanggal1,
            'tanggal2' => $this->tanggal2,
        ]);
    }
}
