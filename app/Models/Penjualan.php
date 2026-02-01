<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    //
    protected $table = 'penjualan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function penjualanDetail(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function stok(): HasMany
    {
        return $this->hasMany(Stok::class);
    }

    public function keuanganJurnal(): HasMany
    {
        return $this->hasMany(KeuanganJurnal::class, 'referensi_id')->where('jenis', 'Penjualan');
    }
}
