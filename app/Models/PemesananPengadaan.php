<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PemesananPengadaan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan_pengadaan';

    public function permintaanPengadaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanPengadaan::class);
    }

    public function pelunasanPemesananPengadaan(): HasOne
    {
        return $this->hasOne(PelunasanPengadaan::class);
    }

    public function pemesananPengadaanDetail(): HasMany
    {
        return $this->hasMany(PemesananPengadaanDetail::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stokMasuk(): HasMany
    {
        return $this->hasMany(StokMasuk::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function jurnalKeuangan(): HasOne
    {
        return $this->hasOne(JurnalKeuangan::class);
    }

    public function stok(): HasMany
    {
        return $this->hasMany(Stok::class);
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(Stok::class)->whereNotNull('stok_keluar_id');
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }

    public function getTotalHargaAttribute(): float
    {
        return $this->pemesananPengadaanDetail->sum(function ($item) {
            return $item->harga_beli * $item->qty;
        }) - $this->diskon + $this->ppn;
    }
}
