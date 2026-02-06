<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengadaanPemesananDetail extends Model
{
    use HasFactory;

    protected $table = 'pengadaan_pemesanan_detail';

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function pengadaanPemesanan(): BelongsTo
    {
        return $this->belongsTo(PengadaanPemesanan::class);
    }

    public function barangSatuan(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class)->with('satuanKonversi');
    }
}
