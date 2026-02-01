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
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function kepegawaianPegawai(): BelongsTo
    {
        return $this->belongsTo(KepegawaianPegawai::class);
    }

    public function getNamaAttribute()
    {
        if ($this->kepegawaian_pegawai_id && $this->kepegawaianPegawai) {
            return $this->kepegawaianPegawai->nama;
        }
        return $this->attributes['nama'] ?? null;
    }

    public function getNikAttribute()
    {
        if ($this->kepegawaian_pegawai_id && $this->kepegawaianPegawai) {
            return $this->kepegawaianPegawai->nik;
        }
        return $this->attributes['nik'] ?? null;
    }

    public function getAlamatAttribute()
    {
        if ($this->kepegawaian_pegawai_id && $this->kepegawaianPegawai) {
            return $this->kepegawaianPegawai->alamat;
        }
        return $this->attributes['alamat'] ?? null;
    }
    
    public function getNoHpAttribute()
    {
        if ($this->kepegawaian_pegawai_id && $this->kepegawaianPegawai) {
            return $this->kepegawaianPegawai->no_hp;
        }
        return $this->attributes['no_hp'] ?? null;
    }

    public function scopeDokter($query)
    {
        return $query->where('dokter', 1);
    }
}
