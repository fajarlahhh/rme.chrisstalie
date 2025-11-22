<?php

namespace App\Models;

use App\Models\Stok;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StokMasuk extends Model
{
    use HasFactory;

    protected $table = 'stok_masuk';

    public function barangSatuan(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function stok(): HasMany
    {
        return $this->hasMany(Stok::class);
    }

    public function keluar(): HasMany
    {
        return $this->hasMany(Stok::class)->whereNotNull('stok_keluar_id');
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function jurnalBarangDagang(): HasOne
    {
        return $this->hasOne(Jurnal::class, 'referensi_id')->where('jenis', 'Stok Masuk Barang Dagang');
    }

    public function jurnalAlatDanBahan(): HasOne
    {
        return $this->hasOne(Jurnal::class, 'referensi_id')->where('jenis', 'Stok Masuk Alat dan Bahan');
    }

    public function jurnalBarangKhusus(): HasOne
    {
        return $this->hasOne(Jurnal::class, 'referensi_id')->where('jenis', 'Stok Masuk Barang Khusus');
    }
}
