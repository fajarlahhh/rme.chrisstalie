<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengadaanPemesanan extends Model
{
    use HasFactory;

    protected $table = 'pengadaan_pemesanan';

    public function pengadaanPermintaan(): BelongsTo
    {
        return $this->belongsTo(PengadaanPermintaan::class);
    }

    public function pengadaanPelunasanPemesanan(): HasOne
    {
        return $this->hasOne(PengadaanPelunasan::class);
    }

    public function pengadaanPemesananDetail(): HasMany
    {
        return $this->hasMany(PengadaanPemesananDetail::class);
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
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function keuanganJurnal(): HasOne
    {
        return $this->hasOne(KeuanganJurnal::class);
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

    public function pengadaanPemesananVerifikasi(): HasOne
    {
        return $this->hasOne(PengadaanVerifikasi::class)->where('jenis', 'Persetujuan Pemesanan Pengadaan');
    }

    public function pengadaanTagihan(): HasOne
    {
        return $this->hasOne(PengadaanTagihan::class);
    }

    public function getTotalHargaAttribute(): float
    {
        return $this->pengadaanPemesananDetail->sum(function ($item) {
            return $item->harga_beli * $item->qty;
        }) - $this->diskon + $this->ppn;
    }
}
