<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifTindakan extends Model
{
    //
    protected $table = 'tarif_tindakan';

    public function tarifTindakanAlatBahan(): HasMany
    {
        return $this->hasMany(TarifTindakanAlatBahan::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }

    public function getBiayaAlatBahanAttribute(): int
    {
        return $this->tarifTindakanAlatBahan->sum(function ($q) {
            return ($q->qty ?? 0) * ($q->barangSatuan->harga_jual ?? 0);
        });
    }

    public function getBiayaTotalAttribute(): int
    {
        return $this->biaya_jasa_dokter + $this->biaya_jasa_perawat + $this->biaya_tidak_langsung + $this->biaya_alat_bahan + $this->biaya_keuntungan_klinik;
    }
}
