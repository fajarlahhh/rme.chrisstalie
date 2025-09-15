<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Aset extends Model
{
    protected $table = 'aset';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }

    public function kodeAkunSumberDana(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_sumber_dana_id');
    }

    public function asetPenyusutan(): HasMany
    {
        return $this->hasMany(AsetPenyusutan::class);
    }

    public function jurnal(): HasOne
    {
        return $this->hasOne(Jurnal::class, 'referensi_id')->where('jenis', 'Pembelian Aset');
    }
}
