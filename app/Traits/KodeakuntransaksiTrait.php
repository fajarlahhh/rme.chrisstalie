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
        $data  = KeuanganKodeAkunTransaksi::whereIn('transaksi', $transaksi)->get();

        if ($data->count() > 1) {
            return $data;
        }

        return $data->first();
    }

    public function getKodeAkunTransaksiByKategori($kategori)
    {
        $data  = KeuanganKodeAkunTransaksi::whereIn('kategori', $kategori)->get();
        if ($data->count() > 1) {
            return $data;
        }

        return $data->first();
    }
}
