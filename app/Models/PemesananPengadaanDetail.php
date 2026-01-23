<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PemesananPengadaanDetail extends Model
{
    use HasFactory;

    protected $table = 'pemesanan_pengadaan_detail';

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function pemesananPengadaan(): BelongsTo
    {
        return $this->belongsTo(PemesananPengadaan::class);
    }

    public function barangSatuan(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class);
    }
}
