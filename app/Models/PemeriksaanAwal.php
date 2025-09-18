<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemeriksaanAwal extends Model
{
    //
    protected $table = 'pemeriksaan_awal';
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'id');
    }

    public function pemeriksaanAwalFisik(): HasMany
    {
        return $this->hasMany(PemeriksaanAwalFisik::class);
    }

    public function pemeriksaanAwalTandaTandaVital(): HasMany
    {
        return $this->hasMany(PemeriksaanAwalTandaTandaVital::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }
}
