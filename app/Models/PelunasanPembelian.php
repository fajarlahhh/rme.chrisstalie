<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelunasanPembelian extends Model
{
    //
    protected $table = 'pelunasan_pemesanan_pengadaan';

    public function pemesananPengadaan()
    {
        return $this->belongsTo(PemesananPengadaan::class);
    }

    public function jurnal()
    {
        return $this->hasOne(Jurnal::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function kodeAkunPembayaran()
    {
        return $this->belongsTo(KodeAkun::class);
    }
}
