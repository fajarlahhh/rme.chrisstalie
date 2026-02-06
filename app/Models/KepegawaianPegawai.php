<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KepegawaianPegawai extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kepegawaian_pegawai';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }
    
    public function kepegawaianPegawaiUnsurGaji(): HasMany
    {
        return $this->hasMany(KepegawaianPegawaiUnsurGaji::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeNonAktif($query)
    {
        return $query->where('status', 'Non Aktif');
    }

    public function kepegawaianAbsensi(): HasMany
    {
        return $this->hasMany(KepegawaianAbsensi::class);
    }

    public function kepegawaianKehadiran(): HasMany
    {
        return $this->hasMany(KepegawaianKehadiran::class);
    }
}
