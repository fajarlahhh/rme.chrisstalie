<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyuid;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $table = 'pengguna';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'uid',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'uid_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the pegawai that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class)->withTrashed();
    }

    public function nakes(): HasOne
    {
        return $this->hasOne(Nakes::class);
    }

    public function dokter(): HasOne
    {
        return $this->hasOne(Nakes::class)->where('dokter', 1);
    }

    public function getNamaAttribute(): string
    {
        if ($this->pegawai && isset($this->pegawai->nama)) {
            return $this->pegawai->nama;
        }
        // Fallback: try to get original 'nama' directly from attributes
        return $this->attributes['nama'] ?? '';
    }

    public function getPanggilanAttribute(): string
    {
        if ($this->pegawai && isset($this->pegawai->panggilan)) {
            return $this->pegawai->panggilan;
        }
        // Fallback: try to get original 'panggilan' directly from attributes
        return $this->attributes['panggilan'] ?? '';
    }
    
}
