<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tug extends Model
{
    //
    protected $table = 'tug';
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'id');
    }
}
