<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelunasanPengadaan extends Model
{
    //
    protected $table = 'pelunasan_pengadaan';

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
