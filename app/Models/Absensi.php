<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absensi extends Model
{
    use SoftDeletes;
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
