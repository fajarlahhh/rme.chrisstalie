<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barang';
    
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function konsinyator(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'konsinyator_id')->withTrashed();
    }

    public function availableStok(): HasMany
    {
        return $this->hasMany(Stok::class)->whereNull('date_out_stok')->whereNull('sale_id')->whereNull('selling_harga');
    }

    public function stok(): HasMany
    {
        return $this->hasMany(Stok::class);
    }

    public function stokSold(): HasMany
    {
        return $this->hasMany(Stok::class)->sold();
    }

    public function scopeAlkes(Builder $query): void
    {
        $query->where('type', 'Alat Kesehatan');
    }

    public function scopeObat(Builder $query): void
    {
        $query->where('type', 'Obat');
    }

    public function stokMasuk(): HasMany
    {
        return $this->hasMany(StokMasuk::class);
    }

    public function scopePersediaan(Builder $query): void
    {
        $query->whereNull('konsinyator_id');
    }

    public function scopeKonsinyasi(Builder $query): void
    {
        $query->whereNotNull('konsinyator_id');
    }

    public function barangSatuan(): HasMany
    {
        return $this->hasMany(BarangSatuan::class);
    }

    public function barangSatuanTerkecil(): HasOne
    {
        return $this->hasOne(BarangSatuan::class)->where('rasio_dari_terkecil', 1);
    }
    
}
