<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResepObat extends Model
{
    //
    use SoftDeletes;
    protected $table = 'resep_obat';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function barangSatuan(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class)->with('satuanKonversi');
    }

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
