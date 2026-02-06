<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeuanganKodeAkunTransaksi extends Model
{
    //
    protected $table = 'keuangan_kode_akun_transaksi';

    public function kodeAkun()
    {
        return $this->belongsTo(KodeAkun::class);
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class)->with('kepegawaianPegawai')->withTrashed();
    }
}
