<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeracikanResepObat extends Model
{
    //
    protected $table = 'peracikan_resep_obat';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }
}
