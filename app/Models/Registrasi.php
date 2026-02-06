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
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'pembayaran_id',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }

    public function nakes(): BelongsTo
    {
        return $this->belongsTo(Nakes::class);
    }

    public function pemeriksaanAwal(): HasOne
    {
        return $this->hasOne(PemeriksaanAwal::class, 'id');
    }

    public function file(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function upload(): HasOne
    {
        return $this->hasOne(Upload::class, 'id');
    }

    public function tug(): HasOne
    {
        return $this->hasOne(Tug::class, 'id');
    }

    public function tindakan(): HasMany
    {
        return $this->hasMany(Tindakan::class);
    }

    public function tindakanBelumPenugasan(): HasMany
    {
        return $this->hasMany(Tindakan::class, 'id')->whereNull('dokter_id')->orWhereNull('perawat_id');
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
        return $this->hasOne(Pembayaran::class);
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
        return $this->hasMany(ResepObat::class)->orderBy('resep');
    }

    public function peracikanResepObat(): HasOne
    {
        return $this->hasOne(PeracikanResepObat::class, 'id');
    }
}
