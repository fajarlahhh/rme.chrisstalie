<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    //
    protected $table = 'pasien';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_daftar' => 'date',
    ];

    public function registrasi(): HasMany
    {
        return $this->hasMany(Registrasi::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function rekamMedis(): HasMany
    {
        return $this->hasMany(Registrasi::class)->orderBy('created_at', 'desc');
    }

    public function getUmurAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }
}
