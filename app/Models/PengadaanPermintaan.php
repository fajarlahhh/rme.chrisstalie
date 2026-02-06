<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanPermintaan extends Model
{
    //
    protected $table = 'pengadaan_permintaan';

    public function pengadaanPermintaanDetail()
    {
        return $this->hasMany(PengadaanPermintaanDetail::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function pengadaanVerifikasi()
    {
        return $this->hasMany(PengadaanVerifikasi::class)->orderBy('created_at', 'desc')->where('jenis', 'Permintaan Pengadaan');
    }

    public function pengadaanPemesanan()
    {
        return $this->hasMany(PengadaanPemesanan::class);
    }

    public function pengadaanPemesananDetail()
    {
        return $this->hasMany(PengadaanPemesananDetail::class);
    }
}
