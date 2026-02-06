<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Icd10 extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'icd10';
    
    /**
     * Get the pengguna that owns the Icd10
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }
}
