<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPoin extends Model
{
    //
    protected $table = 'member_poin';

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
