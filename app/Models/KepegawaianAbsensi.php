<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KepegawaianAbsensi extends Model
{
    use SoftDeletes;
    //
    protected $table = 'kepegawaian_absensi';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['kepegawaian_pegawai_id', 'tanggal', 'masuk', 'pulang', 'izin'];


    public function kepegawaianPegawai(): BelongsTo
    {
        return $this->belongsTo(KepegawaianPegawai::class);
    }
}
