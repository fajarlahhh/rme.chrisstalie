<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifTindakan extends Model
{
    //
    protected $table = 'tarif_tindakan';

    public function tarifTindakanAlatBarang(): HasMany
    {
        return $this->hasMany(TarifTindakanAlatBarang::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }

    public function getBiayaAlatBarangAttribute(): int
    {
        return $this->tarifTindakanAlatBarang->sum(function ($q) {
            return ($q->qty ?? 0) * ($q->barang_id ? $q->barangSatuan->harga_jual : $q->biaya);
        });
    }

    public function getBiayaAlatAttribute(): int
    {
        return $this->tarifTindakanAlatBarang->whereNotNull('aset_id')->sum(function ($q) {
            return $q->qty * $q->biaya;
        });
    }
}
