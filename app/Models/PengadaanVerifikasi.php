<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanVerifikasi extends Model
{
    //
    protected $table = 'pengadaan_verifikasi';

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }
}
