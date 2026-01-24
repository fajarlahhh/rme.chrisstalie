<?php

namespace App\Class;

use App\Models\JurnalKeuangan;
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
        $terakhir = JurnalKeuangan::where('tanggal', 'like', substr($tanggal, 0, 7) . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nomorTerakhir = $terakhir ? (int)substr($terakhir->id, 6, 5) : 0;
        // dd(substr($terakhir->id, 6, 5));
        $nomor = 'JURNAL/' . str_replace('-', '/', substr($tanggal, 0, 7)) . '/' . sprintf('%05d', $nomorTerakhir + 1);
        return $nomor;
    }

    public static function insert($jenis, $sub_jenis = null, $tanggal, $uraian, $system = 0, $aset_id = null, $pemesanan_pengadaan_id = null, $stok_masuk_id = null, $pembayaran_id = null, $penggajian_id = null, $pelunasan_pemesanan_pengadaan_id = null, $stok_keluar_id = null, $detail)
    {
        $nomor = self::getNomor($tanggal);
        
        $jurnalKeuangan = new JurnalKeuangan();
        $jurnalKeuangan->id = str_replace('/', '', substr($nomor, 6, 14));
        $jurnalKeuangan->nomor = $nomor;
        $jurnalKeuangan->jenis = $jenis;
        $jurnalKeuangan->sub_jenis = $sub_jenis;
        $jurnalKeuangan->tanggal = $tanggal;
        $jurnalKeuangan->uraian = $uraian;
        $jurnalKeuangan->system = $system;
        $jurnalKeuangan->aset_id = $aset_id;
        $jurnalKeuangan->pemesanan_pengadaan_id = $pemesanan_pengadaan_id;
        $jurnalKeuangan->stok_masuk_id = $stok_masuk_id;
        $jurnalKeuangan->pembayaran_id = $pembayaran_id;
        $jurnalKeuangan->penggajian_id = $penggajian_id;
        $jurnalKeuangan->pelunasan_pemesanan_pengadaan_id = $pelunasan_pemesanan_pengadaan_id;
        $jurnalKeuangan->stok_keluar_id = $stok_keluar_id;
        $jurnalKeuangan->pengguna_id = auth()->id();
        $jurnalKeuangan->save();

        $jurnalKeuangan->jurnalDetail()->delete();
        $jurnalKeuangan->jurnalDetail()->insert(collect($detail)->map(fn($q) => [
            'jurnal_keuangan_keuangan_keuangan_id' => $jurnalKeuangan->id,
            'debet' => $q['debet'],
            'kredit' => $q['kredit'],
            'kode_akun_id' => $q['kode_akun_id'],
        ])->toArray());

        return $jurnalKeuangan;
    }
}
