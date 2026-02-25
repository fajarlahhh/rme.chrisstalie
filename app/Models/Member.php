<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    //
    protected $table = 'member';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'id');
    }

    public function memberSaldo(): HasMany
    {
        return $this->hasMany(MemberSaldo::class);
    }

    public function memberSaldoTerakhir(): HasOne
    {
        return $this->hasOne(MemberSaldo::class)->orderBy('created_at', 'desc');
    }

    public function memberPoin(): HasMany
    {
        return $this->hasMany(MemberPoin::class);
    }

    public function memberPointTerakhir(): HasOne
    {
        return $this->hasOne(MemberPoin::class)->orderBy('created_at', 'desc');
    }
}
