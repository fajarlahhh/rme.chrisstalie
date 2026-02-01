<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanPelunasan extends Model
{
    //
    protected $table = 'pengadaan_pelunasan';

    public function pengadaanPemesanan()
    {
        return $this->belongsTo(PengadaanPemesanan::class);
    }

    public function keuanganJurnal()
    {
        return $this->hasOne(KeuanganJurnal::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function kodeAkunPembayaran()
    {
        return $this->belongsTo(KodeAkun::class);
    }
}
