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
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }

    public function kodeAkunSumberDana(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_sumber_dana_id');
    }

    public function keuanganJurnal(): HasOne
    {
        return $this->hasOne(KeuanganJurnal::class);
    }
}
