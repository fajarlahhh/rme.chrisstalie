<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->hasMany(MemberSaldo::class)->orderBy('created_at', 'desc');
    }

    public function memberPoin(): HasMany
    {
        return $this->hasMany(MemberPoin::class)->orderBy('created_at', 'desc');
    }
    
    public function memberPembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pasien_id', 'id');
    }

    public function getSaldoAttribute()
    {
        return $this->memberSaldo->sum(fn($q) => $q->masuk - $q->keluar);
    }

    public function getPoinAttribute()
    {
        return $this->memberPoin->sum(fn($q) => $q->masuk - $q->keluar);
    }

    public function getLevelAttribute()
    {
        $pembayaran = $this->memberPembayaran()->where(DB::raw('year(tanggal)'), date('Y'))->sum('total_tagihan');
        if ($pembayaran < 5000000) {
            return 'Bronze';
        } else if ($pembayaran >= 5000000 && $pembayaran < 7000000) {
            return 'Silver';
        } else if ($pembayaran >= 7000000 && $pembayaran < 12000000) {
            return 'Gold';
        } else if ($pembayaran >= 12000000) {
            return 'Diamond';
        }
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}
