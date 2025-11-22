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

    public static function insert($jenis, $tanggal, $uraian, $system = 0, $aset_id = null, $pembelian_id = null, $stok_masuk_id = null, $detail)
    {
        $id = Str::uuid();

        $jurnal = new Jurnal();
        $jurnal->id = $id;
        $jurnal->jenis = $jenis;
        $jurnal->tanggal = $tanggal;
        $jurnal->uraian = $uraian;
        $jurnal->system = $system;
        $jurnal->aset_id = $aset_id;
        $jurnal->pembelian_id = $pembelian_id;
        $jurnal->stok_masuk_id = $stok_masuk_id;
        $jurnal->pengguna_id = auth()->id();
        $jurnal->save();

        $jurnal->jurnalDetail()->delete();
        $jurnal->jurnalDetail()->insert(collect($detail)->map(fn($q) => [
            'jurnal_id' => $id,
            'debet' => $q['debet'],
            'kredit' => $q['kredit'],
            'kode_akun_id' => $q['kode_akun_id'],
        ])->toArray());
    }

    public static function pembelianPersediaan($jenis, $tanggal, $uraian, $ppn, $diskon, $kode_akun_id, $referensi_id, $barang)
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
        if (isset($ppn)) {
            $jurnalDetail[] = [
                'jurnal_id' => $id,
                'debet' => $ppn,
                'kredit' => 0,
                'kode_akun_id' => '11400'
            ];
        }

        //Diskon
        if (isset($diskon)) {
            if ($diskon > 0) {
                $jurnalDetail[] = [
                    'jurnal_id' => $id,
                    'debet' => 0,
                    'kredit' => $diskon,
                    'kode_akun_id' => '45000'
                ];
            }
        }

        //Jenis Bayar
        $jurnalDetail[] = [
            'jurnal_id' => $id,
            'debet' => 0,
            'kredit' => collect($barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']) - (isset($diskon) ? $diskon : 0) + (isset($ppn) ? $ppn : 0),
            'kode_akun_id' => $kode_akun_id
        ];

        self::insert($jenis, $tanggal, $uraian, 1, null, $referensi_id, null, $jurnalDetail);
    }
}
