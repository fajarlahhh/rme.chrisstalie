<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokKeluar extends Model
{
    use HasFactory;

    protected $table = 'stok_keluar';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
    
    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function barangSatuan(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class);
    }
}
