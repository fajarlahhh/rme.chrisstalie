<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class KodeAkun extends Model
{
    //
    protected $table = 'kode_akun';

    public function kodeAkunNeraca()
    {
        return $this->hasMany(KodeAkunNeraca::class);
    }

    public function parent()
    {
        return $this->belongsTo(KodeAkun::class, 'parent_id');
    }

    public function jurnalKeuanganDetail()
    {
        return $this->hasMany(JurnalKeuanganDetail::class);
    }

    public function children()
    {
        return $this->hasMany(KodeAkun::class, 'parent_id');
    }
    
    public function scopeKewajiban(Builder $query): void
    {
        $query->where('kategori', 'Kewajiban');
    }

    public function scopeAktiva(Builder $query): void
    {
        $query->where('kategori', 'Aktiva');
    }

    public function scopeModal(Builder $query): void
    {
        $query->where('kategori', 'Modal');
    }

    public function scopePendapatan(Builder $query): void
    {
        $query->where('kategori', 'Pendapatan');
    }

    public function scopeBeban(Builder $query): void
    {
        $query->where('kategori', 'Beban');
    }

    public function scopeDetail(Builder $query): void
    {
        $query->where('detail', 1);
    }
}
