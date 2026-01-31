<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KepegawaianPenggajian extends Model
{
    //
    protected $table = 'kepegawaian_penggajian';

    protected $casts = [
        'detail' => 'array',
    ];

    public function kodeAkunPembayaran(): BelongsTo
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun_pembayaran_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function kepegawaianPegawai(): BelongsTo
    {
        return $this->belongsTo(KepegawaianPegawai::class);
    }
}
