<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalKeuangan extends Model
{
    //
    protected $table = 'jurnal_keuangan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function jurnalKeuanganDetail(): HasMany
    {
        return $this->hasMany(JurnalKeuanganDetail::class)->orderBy('kode_akun_id', 'asc');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
