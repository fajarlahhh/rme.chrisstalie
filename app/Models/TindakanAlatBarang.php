<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakanAlatBarang extends Model
{
    //
    protected $table = 'tindakan_alat_barang';

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'id');
    }
    
    public function tarifTindakan(): BelongsTo
    {
        return $this->belongsTo(TarifTindakan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class)->whereNotNull('barang_id');
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Aset::class)->whereNotNull('aset_id');
    }

    public function barangSatuan(): BelongsTo
    {
        return $this->belongsTo(BarangSatuan::class);
    }
}
