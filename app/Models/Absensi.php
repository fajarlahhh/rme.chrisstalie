<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Absensi extends Model
{
    //
    protected $table = 'absensi';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['pegawai_id', 'tanggal', 'masuk', 'pulang', 'izin'];


    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jadwalShiftDetail(): BelongsTo
    {
        return $this->belongsTo(JadwalShiftDetail::class);
    }
}
