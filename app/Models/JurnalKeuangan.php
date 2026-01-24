<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalKeuangan extends Model
{
    //
    protected $table = 'jurnalKeuangan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function jurnalDetail(): HasMany
    {
        return $this->hasMany(JurnalDetail::class)->orderBy('kode_akun_id', 'asc');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
