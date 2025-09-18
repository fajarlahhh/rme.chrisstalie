<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Registrasi;

class Diagnosis extends Model
{
    //
    protected $table = 'diagnosis';
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function registrasi()
    {
        return $this->belongsTo(Registrasi::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }
}
