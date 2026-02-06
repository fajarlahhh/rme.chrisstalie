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
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function stokTersedia(): HasMany
    {
        return $this->hasMany(Stok::class)->whereNull('stok_keluar_id');
    }

    public function stok(): HasMany
    {
        return $this->hasMany(Stok::class);
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

    public function stokAwal(): HasMany
    {
        return $this->hasMany(StokAwal::class);
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluar::class);
    }

    public function scopeKlinik(Builder $query): void
    {
        $query->where('persediaan', 'Klinik');
    }

    public function scopeApotek(Builder $query): void
    {
        $query->where('persediaan', 'Apotek');
    }

    public function barangSatuan(): HasMany
    {
        return $this->hasMany(BarangSatuan::class)->with('satuanKonversi');
    }

    public function barangSatuanTerkecil(): HasOne
    {
        return $this->hasOne(BarangSatuan::class)->with('satuanKonversi')->where('rasio_dari_terkecil', 1);
    }

    public function barangSatuanUtama(): HasOne
    {
        return $this->hasOne(BarangSatuan::class)->with('satuanKonversi')->where('utama', 1);
    }

    public function kodeAkun(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class);
    }

    public function kodeAkunPenjualan(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_penjualan_id');
    }

    public function kodeAkunModal(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_modal_id');
    }
}
