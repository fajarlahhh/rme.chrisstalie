<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class DatamastertariftindakanExport implements FromView
{
    use Exportable;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        //
        return view('livewire.datamaster.tariftindakan.cetak', [
            'cetak' => true,
            'data' => $this->data,
        ]);
    }
}
