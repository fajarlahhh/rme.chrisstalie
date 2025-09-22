<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registrasi extends Model
{
    //
    protected $table = 'registrasi';

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function nakes(): BelongsTo
    {
        return $this->belongsTo(Nakes::class);
    }

    public function pemeriksaanAwal(): HasOne
    {
        return $this->hasOne(PemeriksaanAwal::class, 'id');
    }

    public function tug(): HasOne
    {
        return $this->hasOne(Tug::class, 'id');
    }

    public function tindakan(): HasOne
    {
        return $this->hasOne(Tindakan::class, 'id');
    }

    public function diagnosis(): HasOne
    {
        return $this->hasOne(Diagnosis::class, 'id');
    }

    public function siteMarking(): HasMany
    {
        return $this->hasMany(SiteMarking::class, 'id');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id');
    }
}
