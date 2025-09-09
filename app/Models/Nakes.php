<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nakes extends Model
{
    //
    protected $table = 'nakes';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function getNamaAttribute()
    {
        return $this->pegawai_id ? $this->pegawai->nama : $this->nama;
    }

    public function getNikAttribute()
    {
        return $this->pegawai_id ? $this->pegawai->nik : $this->nik;
    }

    public function getAlamatAttribute()
    {
        return $this->pegawai_id ? $this->pegawai->alamat : $this->alamat;
    }
    
    public function getNoHpAttribute()
    {
        return $this->pegawai_id ? $this->pegawai->no_hp : $this->no_hp;
    }
}
