<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengadaanTagihan extends Model
{
    protected $table = 'pengadaan_tagihan';

    public function pengadaanPemesanan(): BelongsTo
    {
        return $this->belongsTo(PengadaanPemesanan::class);
    }

    public function pengadaanPelunasan(): HasOne
    {
        return $this->hasOne(PengadaanPelunasanDetail::class);
    }

    public function pengadaanPelunasanDetail(): HasOne
    {
        return $this->hasOne(PengadaanPelunasanDetail::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function keuanganJurnal(): HasOne
    {
        return $this->hasOne(KeuanganJurnal::class);
    }
}
