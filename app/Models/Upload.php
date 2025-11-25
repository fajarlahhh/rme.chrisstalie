<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upload extends Model
{
    //
    protected $table = 'upload';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'id');
    }
}
