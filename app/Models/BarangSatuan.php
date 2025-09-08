<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangSatuan extends Model
{
    //
    protected $table = 'barang_satuan';
    protected $fillable = ['barang_id', 'nama', 'harga_jual', 'rasio_dari_terkecil', 'satuan_konversi_id'];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function getKonversiSatuanAttribute()
    {
        if ($this->rasio_dari_terkecil == 1) {
            return null;
        }

        $satuanKonversi = $this->satuanKonversi;

        $rasio = $this->rasio_dari_terkecil / ($satuanKonversi->rasio_dari_terkecil ?? 1);
        $nama = $satuanKonversi->nama ?? '';

        return trim($rasio . ' ' . $nama);
    }

    public function satuanKonversi(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class, 'satuan_konversi_id');
    }
}
