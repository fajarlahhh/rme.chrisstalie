<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPengadaan extends Model
{
    //
    protected $table = 'permintaan_pengadaan';

    public function permintaanPengadaanDetail()
    {
        return $this->hasMany(PermintaanPengadaanDetail::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function verifikasiPengadaan()
    {
        return $this->hasMany(VerifikasiPengadaan::class)->orderBy('created_at', 'desc');
    }

    public function VerifikasiPengadaanPending()
    {
        return $this->hasMany(VerifikasiPengadaan::class)->whereNull('status');
    }

    public function VerifikasiPengadaanDisetujui()
    {
        return $this->hasMany(VerifikasiPengadaan::class)->where('status', 'Disetujui');
    }

    public function VerifikasiPengadaanDitolak()
    {
        return $this->hasMany(VerifikasiPengadaan::class)->where('status', 'Ditolak');
    }

    public function pemesananPengadaan()
    {
        return $this->hasOne(PemesananPengadaan::class);
    }

    public function stokMasuk()
    {
        return $this->hasMany(StokMasuk::class);
    }
}
