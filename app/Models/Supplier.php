<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supplier';
    
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function scopeConsignment($query)
    {
        return $query->where('konsinyasi', 1);
    }

    public function scopeGeneral($query)
    {
        return $query->where('konsinyasi', 0);
    }
}
