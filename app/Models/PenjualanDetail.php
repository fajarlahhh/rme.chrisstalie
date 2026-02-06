<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PenjualanDetail extends Model
{
    //
    protected $table = 'penjualan_detail';

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
    
    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barangSatuan(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class)->with('satuanKonversi');
    }

    public function stokKeluar(): HasOne
    {
        return $this->hasOne(Stok::class);
    }
}
