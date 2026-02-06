<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetodeBayar extends Model
{
    //
    protected $table = 'metode_bayar';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }
}
