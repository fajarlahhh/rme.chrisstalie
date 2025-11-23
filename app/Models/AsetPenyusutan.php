<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsetPenyusutan extends Model
{
    //
    protected $table = 'aset_penyusutan';

    public function aset(): BelongsTo
    {
        return $this->belongsTo(Aset::class);
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class);
    }
}
