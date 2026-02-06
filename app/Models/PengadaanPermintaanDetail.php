<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanPermintaanDetail extends Model
{
    //
    protected $table = 'pengadaan_permintaan_detail';
    public $timestamps = false;

    public function pengadaanPermintaan()
    {
        return $this->belongsTo(PengadaanPermintaan::class);
    }

    public function barangSatuan()
    {
        return $this->belongsTo(BarangSatuan::class)->with('satuanKonversi');
    }
}
