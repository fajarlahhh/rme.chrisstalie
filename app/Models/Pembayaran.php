<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    //
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class);
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }
}
