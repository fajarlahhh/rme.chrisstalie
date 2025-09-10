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

    public static function pengeluaranBarang($jenis, $data, $detail)
    {
        $id = Str::uuid();

        $jurnal = new Jurnal();
        $jurnal->id = $id;
        $jurnal->jenis = $jenis;
        $jurnal->tanggal = $data['tanggal'];
        $jurnal->uraian = $data['uraian'];
        $jurnal->unit_bisnis = $data['unit_bisnis'];
        $jurnal->pengguna_id = auth()->id();
        $jurnal->save();

        $jurnal->jurnalDetail()->delete();
        $jurnal->jurnalDetail()->insert(collect($detail)->map(fn($q, $index) => [
            'debit' => $q['debit'],
            'kredit' => $q['kredit'],
            'kode_akun_id' => $q['kode_akun_id']
        ])->toArray());
    }
}
