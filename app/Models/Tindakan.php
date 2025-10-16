<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tindakan extends Model
{
    //
    protected $table = 'tindakan';

    public function registrasi(): BelongsTo
    {
        return $this->belongsTo(Registrasi::class, 'id');
    }

    public function tarifTindakan(): BelongsTo
    {
        return $this->belongsTo(TarifTindakan::class);
    }
}
