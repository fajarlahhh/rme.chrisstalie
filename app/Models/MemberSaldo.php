<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSaldo extends Model
{
    //
    protected $table = 'member_saldo';

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }
}

