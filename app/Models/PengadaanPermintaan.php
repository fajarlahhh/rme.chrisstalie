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
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function pengadaanVerifikasi()
    {
        return $this->hasMany(PengadaanVerifikasi::class)->orderBy('created_at', 'desc');
    }

    public function pengadaanVerifikasiPending()
    {
        return $this->hasMany(PengadaanVerifikasi::class)->where('jenis', 'Permintaan Pengadaan')->whereNull('status');
    }

    public function pengadaanVerifikasiDisetujui()
    {
        return $this->hasMany(PengadaanVerifikasi::class)->where('jenis', 'Permintaan Pengadaan')->where('status', 'Disetujui');
    }

    public function pengadaanVerifikasiDitolak()
    {
        return $this->hasMany(PengadaanVerifikasi::class)->where('jenis', 'Permintaan Pengadaan')->where('status', 'Ditolak');
    }

    public function pengadaanPemesanan()
    {
        return $this->hasOne(PengadaanPemesanan::class);
    }

    public function stokMasuk()
    {
        return $this->hasMany(StokMasuk::class);
    }
}
