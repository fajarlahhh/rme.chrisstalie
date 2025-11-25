<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalShift extends Model
{
    //
    protected $table = 'jadwal_shift';

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'time',
        'jam_pulang' => 'time',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jadwalShiftDetail(): HasMany
    {
        return $this->hasMany(JadwalShiftDetail::class);
    }
}
