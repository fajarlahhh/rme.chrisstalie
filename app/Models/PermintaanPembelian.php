<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPembelian extends Model
{
    //
    protected $table = 'permintaan_pembelian';

    public function permintaanPembelianDetail()
    {
        return $this->hasMany(PermintaanPembelianDetail::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function verifikasi()
    {
        return $this->hasMany(Verifikasi::class)->orderBy('created_at', 'desc');
    }

    public function verifikasiPending()
    {
        return $this->hasMany(Verifikasi::class)->whereNull('status');
    }

    public function verifikasiDisetujui()
    {
        return $this->hasMany(Verifikasi::class)->where('status', 'Disetujui');
    }

    public function verifikasiDitolak()
    {
        return $this->hasMany(Verifikasi::class)->where('status', 'Ditolak');
    }

    public function pembelian()
    {
        return $this->hasOne(Pembelian::class);
    }

    public function stokMasuk()
    {
        return $this->hasMany(StokMasuk::class);
    }
}
