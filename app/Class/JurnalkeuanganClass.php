<?php

namespace App\Class;

use App\Models\KeuanganJurnal;
use App\Models\KeuanganSaldo;
use Illuminate\Support\Str;

class JurnalkeuanganClass
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function getNomor($tanggal)
    {
        $terakhir = KeuanganJurnal::where('tanggal', 'like', substr($tanggal, 0, 7) . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nomorTerakhir = $terakhir ? (int)substr($terakhir->id, 6, 5) : 0;
        // dd(substr($terakhir->id, 6, 5));
        $nomor = 'JURNAL/' . str_replace('-', '/', substr($tanggal, 0, 7)) . '/' . sprintf('%05d', $nomorTerakhir + 1);
        return $nomor;
    }

    public static function insert($jenis, $sub_jenis = null, $tanggal, $uraian, $system = 0, $foreign_key = null, $foreign_id = null, $detail)
    {
        $nomor = self::getNomor($tanggal);

        $keuanganJurnal = new KeuanganJurnal();
        $keuanganJurnal->id = str_replace('/', '', substr($nomor, 6, 14));
        $keuanganJurnal->nomor = $nomor;
        $keuanganJurnal->jenis = $jenis;
        $keuanganJurnal->sub_jenis = $sub_jenis;
        $keuanganJurnal->tanggal = $tanggal;
        $keuanganJurnal->uraian = $uraian;
        $keuanganJurnal->system = $system;

        // Pastikan foreign_key adalah string dan tidak null lalu set jika benar
        if ($foreign_key !== null && is_string($foreign_key) && $foreign_key !== '') {
            $keuanganJurnal->{$foreign_key} = $foreign_id;
        }

        $keuanganJurnal->pengguna_id = auth()->id();
        $keuanganJurnal->save();

        $keuanganJurnal->keuanganJurnalDetail()->delete();
        $keuanganJurnal->keuanganJurnalDetail()->insert(collect($detail)->map(fn($q) => [
            'keuangan_jurnal_id' => $keuanganJurnal->id,
            'debet' => $q['debet'],
            'kredit' => $q['kredit'],
            'kode_akun_id' => $q['kode_akun_id'],
        ])->toArray());

        return $keuanganJurnal;
    }

    public static function tutupBuku($tanggal) : bool
    {
        $periode = \Carbon\Carbon::parse($tanggal)->addMonth()->format('Y-m-01');
        $keuanganJurnal = KeuanganSaldo::where('periode', $periode)->get();
        if ($keuanganJurnal->count() > 0) {
            return true;
        }
        return false;
    }
}
