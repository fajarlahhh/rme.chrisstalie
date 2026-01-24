<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\KodeAkun;

class JurnalKeuanganDetail extends Model
{
    //
    protected $table = 'jurnal_keuangan_detail';

    public function jurnalKeuangan(): BelongsTo
    {
        return $this->belongsTo(JurnalKeuangan::class);
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }
}
