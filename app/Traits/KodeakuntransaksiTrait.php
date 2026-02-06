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

    public function getAkunTransaksiByTransaksi($transaksi)
    {
        return KeuanganKodeAkunTransaksi::where('transaksi', $transaksi)->first();
    }
}
