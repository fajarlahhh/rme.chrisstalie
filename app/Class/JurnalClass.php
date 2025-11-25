<?php

namespace App\Class;

use App\Models\Jurnal;
use Illuminate\Support\Str;

class JurnalClass
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function insert($jenis, $sub_jenis = null, $tanggal, $uraian, $system = 0, $aset_id = null, $pembelian_id = null, $stok_masuk_id = null, $pembayaran_id = null, $penggajian_id = null, $pelunasan_pembelian_id = null, $detail)
    {
        $terakhir = Jurnal::where('tanggal', 'like', substr($tanggal, 0, 7) . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nomorTerakhir = $terakhir ? (int)substr($terakhir->id, 12, 5) : 0;
        $nomor = 'JURNAL/' . str_replace('-', '/', substr($tanggal, 0, 7)) . '/' . sprintf('%05d', $nomorTerakhir + 1);
        
        $jurnal = new Jurnal();
        $jurnal->id = str_replace('/', '', $nomor);
        $jurnal->nomor = $nomor;
        $jurnal->jenis = $jenis;
        $jurnal->sub_jenis = $sub_jenis;
        $jurnal->tanggal = $tanggal;
        $jurnal->uraian = $uraian;
        $jurnal->system = $system;
        $jurnal->aset_id = $aset_id;
        $jurnal->pembelian_id = $pembelian_id;
        $jurnal->stok_masuk_id = $stok_masuk_id;
        $jurnal->pembayaran_id = $pembayaran_id;
        $jurnal->penggajian_id = $penggajian_id;
        $jurnal->pelunasan_pembelian_id = $pelunasan_pembelian_id;
        $jurnal->pengguna_id = auth()->id();
        $jurnal->save();

        $jurnal->jurnalDetail()->delete();
        $jurnal->jurnalDetail()->insert(collect($detail)->map(fn($q) => [
            'jurnal_id' => $jurnal->id,
            'debet' => $q['debet'],
            'kredit' => $q['kredit'],
            'kode_akun_id' => $q['kode_akun_id'],
        ])->toArray());

        return $jurnal;
    }
}
