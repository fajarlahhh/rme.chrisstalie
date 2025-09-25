<?php

namespace App\Class;

use App\Models\Jurnal;
use Illuminate\Support\Str;

class JurnalClass
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function insert($id, $jenis, $data, $detail)
    {
        $jurnal = new Jurnal();
        $jurnal->id = $id;
        $jurnal->jenis = $jenis;
        $jurnal->tanggal = $data['tanggal'];
        $jurnal->uraian = $data['uraian'];
        $jurnal->unit_bisnis = $data['unit_bisnis'];
        $jurnal->referensi_id = $data['referensi_id'];
        $jurnal->pengguna_id = auth()->id();
        $jurnal->save();

        $jurnal->jurnalDetail()->delete();
        $jurnal->jurnalDetail()->insert($detail);
    }
}
