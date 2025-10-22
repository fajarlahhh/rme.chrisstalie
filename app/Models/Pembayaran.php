<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembayaran extends Model
{
    //
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluar::class);
    }

    public function jurnalPenjualanBarangBebas(): HasMany
    {
        return $this->hasMany(Jurnal::class, 'referensi_id')->where('jenis', 'Penjualan Barang Bebas');
    }

    public function metodeBayar(): BelongsTo
    {
        return $this->belongsTo(MetodeBayar::class, 'metode_bayar', 'nama');
    }
}
