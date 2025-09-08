<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPembelianDetail extends Model
{
    //
    protected $table = 'permintaan_pembelian_detail';

    public function permintaanPembelian()
    {
        return $this->belongsTo(PermintaanPembelian::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function barangSatuan()
    {
        return $this->belongsTo(BarangSatuan::class);
    }
}
