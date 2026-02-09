<?php

namespace App\Traits;

use App\Models\KeuanganKodeAkunTransaksi;

trait KodeakuntransaksiTrait
{
    //
    public function getAkunTransaksi($jenis)
    {
        return KeuanganKodeAkunTransaksi::where('jenis', $jenis)->get();
    }

    public function getKodeAkunTransaksiByTransaksi($transaksi)
    {
        $data  = KeuanganKodeAkunTransaksi::where('transaksi', $transaksi)->get();

        if ($data->count() > 1) {
            return $data;
        }

        return $data->first();
    }
}
