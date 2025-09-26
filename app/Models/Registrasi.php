<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registrasi extends Model
{
    //
    protected $table = 'registrasi';

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function nakes(): BelongsTo
    {
        return $this->belongsTo(Nakes::class);
    }

    public function pemeriksaanAwal(): HasOne
    {
        return $this->hasOne(PemeriksaanAwal::class, 'id');
    }

    public function tug(): HasOne
    {
        return $this->hasOne(Tug::class, 'id');
    }

    public function tindakan(): HasMany
    {
        return $this->hasMany(Tindakan::class, 'id');
    }

    public function tindakanBelumPenugasan(): HasMany
    {
        return $this->hasMany(Tindakan::class, 'id')->whereNull('dokter_id')->whereNull('perawat_id');
    }

    public function tindakanDenganInformConsent(): HasMany
    {
        return $this->hasMany(Tindakan::class, 'id')->with('tarifTindakan')->where('membutuhkan_inform_consent', 1);
    }

    public function diagnosis(): HasOne
    {
        return $this->hasOne(Diagnosis::class, 'id');
    }

    public function siteMarking(): HasMany
    {
        return $this->hasMany(SiteMarking::class, 'id');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id');
    }

    public function informedConsent(): HasOne
    {
        return $this->hasOne(InformedConsent::class, 'id');
    }

    public function informedConsentDenganFile(): HasOne
    {
        return $this->hasOne(InformedConsent::class, 'id')->whereNotNull('file');
    }

    public function resepObat(): HasMany
    {
        return $this->hasMany(ResepObat::class, 'id');
    }
}
