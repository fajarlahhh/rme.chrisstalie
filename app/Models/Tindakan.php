<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tindakan extends Model
{
    //
    protected $table = 'tindakan';
    
    public function tindakanAlatBarang(): HasMany
    {
        return $this->hasMany(TindakanAlatBarang::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class);
    }

    public function barangSatuan(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class)->with('satuanKonversi');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function tarifTindakan(): BelongsTo
    {
        return $this->belongsTo(TarifTindakan::class);
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'registrasi_id', 'registrasi_id');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Nakes::class, 'dokter_id');
    }

    public function perawat(): BelongsTo
    {
        return $this->belongsTo(Nakes::class);
    }
}
