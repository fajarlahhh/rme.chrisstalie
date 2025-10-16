<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Registrasi;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diagnosis extends Model
{
    //
    protected $table = 'diagnosis';
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class, 'id');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function getIcd10UraianAttribute()
    {
        // Ambil array kode dari kolom icd10 (JSON)
        $codes = collect(json_decode($this->icd10, true))
            ->pluck('icd10')
            ->filter()
            ->unique()
            ->toArray();

        // Relasi manual ke model Icd10 berdasarkan kode
        return Icd10::whereIn('id', $codes)->get();
    }

    public function file(): HasMany
    {
        return $this->hasMany(File::class, 'referensi_id')->where('jenis', 'Diagnosis');
    }
}
