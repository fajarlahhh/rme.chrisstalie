<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Registrasi;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diagnosis extends Model
{
    //
    protected $table = 'diagnosis';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $casts = [
        'icd10' => 'array',
    ];

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
        $codes = collect($this->icd10)
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
