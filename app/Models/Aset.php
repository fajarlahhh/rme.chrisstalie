<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Aset extends Model
{
    protected $table = 'aset';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }

    public function kodeAkunSumberDana(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_sumber_dana_id');
    }

    public function asetPenyusutanGarisLurus(): HasMany
    {
        return $this->hasMany(AsetPenyusutanGarisLurus::class);
    }

    public function asetPenyusutanGarisLurusTerjurnal(): HasMany
    {
        return $this->hasMany(AsetPenyusutanGarisLurus::class)->whereNotNull('jurnal_id');
    }

    public function asetPenyusutanUnitProduksiTerjurnal(): HasMany
    {
        return $this->hasMany(AsetPenyusutanUnitProduksi::class)->whereNotNull('jurnal_id');
    }

    public function asetPenyusutanUnitProduksi(): HasMany
    {
        return $this->hasMany(AsetPenyusutanUnitProduksi::class);
    }

    public function jurnal(): HasOne
    {
        return $this->hasOne(Jurnal::class);
    }
}
