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
        return $this->belongsTo(Pengguna::class)->withTrashed();
    }

    public function getIcd10UraianAttribute()
    {
        // Relasi manual ke model Icd10 berdasarkan kode
        return Icd10::whereIn('id', $this->icd10)->get();
    }

    public function file(): HasMany
    {
        return $this->hasMany(File::class, 'registrasi_id');
    }
}
