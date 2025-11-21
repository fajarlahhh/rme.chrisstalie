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
        $jurnal->referensi_id = $data['referensi_id'];
        $jurnal->pengguna_id = $data['pengguna_id'];
        $jurnal->save();

        $jurnal->jurnalDetail()->delete();
        $jurnal->jurnalDetail()->insert($detail);
    }

    public static function pembelianPersediaan($data, $barang, $jenis)
    {
        $id = Str::uuid();
        $jurnalDetail = [];

        //Barang
        foreach (
            collect($barang)->groupBy('kode_akun_id')->map(fn($q) => [
                'kode_akun_id' => $q->first()['kode_akun_id'],
                'total' => $q->sum(fn($q) => $q['harga_beli'] * $q['qty']),
            ]) as $brg
        ) {
            $jurnalDetail[] = [
                'jurnal_id' => $id,
                'debet' => $brg['total'],
                'kredit' => 0,
                'kode_akun_id' => $brg['kode_akun_id']
            ];
        }

        //PPN Masukan
        $jurnalDetail[] = [
            'jurnal_id' => $id,
            'debet' => $data->ppn,
            'kredit' => 0,
            'kode_akun_id' => '11400'
        ];

        //Diskon
        if ($data->diskon > 0) {
            $jurnalDetail[] = [
                'jurnal_id' => $id,
                'debet' => 0,
                'kredit' => $data->diskon,
                'kode_akun_id' => '45000'
            ];
        }

        //Jenis Bayar
        $jurnalDetail[] = [
            'jurnal_id' => $id,
            'debet' => 0,
            'kredit' => collect($barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']) - $data->diskon + $data->ppn,
            'kode_akun_id' => $data->kode_akun_id
        ];

        self::insert($id, $jenis, [
            'tanggal' => $data['tanggal'],
            'uraian' => $data['uraian'],
            'referensi_id' => $data['id'],
            'pengguna_id' => $data['pengguna_id'],
        ], $jurnalDetail);
    }
}
