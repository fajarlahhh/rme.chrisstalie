<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeuanganJurnal extends Model
{
    //
    protected $table = 'keuangan_jurnal';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function keuanganJurnalDetail(): HasMany
    {
        return $this->hasMany(KeuanganJurnalDetail::class)->orderBy('kode_akun_id', 'asc');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }
}
