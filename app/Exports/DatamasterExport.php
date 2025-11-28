<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class DatamasterExport implements FromView
{
    use Exportable;
    public $data, $jenis;

    public function __construct($data, $jenis)
    {
        $this->data = $data;
        $this->jenis = $jenis;
    }

    public function view(): View
    {
        //
        return view('livewire.datamaster.' . $this->jenis . '.tabel', [
            'cetak' => true,
            'data' => $this->data,
        ]);
    }
}
