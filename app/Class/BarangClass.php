<?php

namespace App\Class;

use App\Models\Stok;
use App\Models\Barang;
use App\Models\StokKeluar;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BarangClass
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function getBarang($persediaan = null, $khusus = null, $resep = null)
    {
        return Barang::select(
            'barang.id as barang_id',
            'barang.nama as barang_nama',
            'barang_satuan.id as barang_satuan_id',
            'barang_satuan.nama as barang_satuan_nama',
            'barang_satuan.rasio_dari_terkecil',
            'barang_satuan.harga_jual',
            'persediaan',
            'kode_akun_id',
            'kode_akun_penjualan_id',
            'kode_akun_modal_id'
        )->leftJoin('barang_satuan', 'barang.id', '=', 'barang_satuan.barang_id')
            ->with('barangSatuan.satuanKonversi')
            ->when($khusus, fn($q) => $q->where('khusus', $khusus))
            ->when($persediaan, fn($q) => $q->where('persediaan', $persediaan))
            ->when($resep == '0' || $resep == '1', fn($q) => $q->where('perlu_resep', $resep))
            ->orderBy('barang.nama')->get()->map(fn($q) => [
                'id' => $q['barang_satuan_id'],
                'nama' => $q['barang_nama'],
                'barang_id' => $q['barang_id'],
                'biaya' => $q['harga_jual'],
                'harga' => $q['harga_jual'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
                'satuan' => $q['barang_satuan_nama'],
                'persediaan' => $q['persediaan'],
                'kode_akun_id' => $q['kode_akun_id'],
                'kode_akun_penjualan_id' => $q['kode_akun_penjualan_id'],
                'kode_akun_modal_id' => $q['kode_akun_modal_id'],
            ])->toArray();
    }

    public static function getBarangBySatuanUtama($persediaan = null, $khusus = 0)
    {
        return Barang::select(
            'barang.id as barang_id',
            'barang.nama as barang_nama',
            'barang_satuan.id as barang_satuan_id',
            'barang_satuan.nama as barang_satuan_nama',
            'barang_satuan.rasio_dari_terkecil',
            'barang_satuan.harga_jual',
            'kode_akun_id',
            'kode_akun_penjualan_id'
        )->leftJoin('barang_satuan', 'barang.id', '=', 'barang_satuan.barang_id')
            ->with('barangSatuan.satuanKonversi')
            ->where('barang_satuan.utama', 1)
            ->where('khusus', $khusus)
            ->when($persediaan, fn($q) => $q->where('persediaan', $persediaan))
            ->orderBy('barang.nama')->get()->map(fn($q) => [
                'id' => $q['barang_satuan_id'],
                'nama' => $q['barang_nama'],
                'barang_id' => $q['barang_id'],
                'biaya' => $q['harga_jual'],
                'harga' => $q['harga_jual'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
                'satuan' => $q['barang_satuan_nama'],
                'kode_akun_id' => $q['kode_akun_id'],
                'kode_akun_penjualan_id' => $q['kode_akun_penjualan_id'],
            ])->toArray();
    }

    public static function hapusStok($barangId, $qty, $stokMasukId)
    {
        $stok = Stok::where('barang_id', $barangId)
            ->where('stok_masuk_id', $stokMasukId)
            ->whereNull('stok_keluar_id')->get();
        if ($stok->count() < $qty) {
            session()->flash('error', 'Stok sudah digunakan');
            return false;
        }
        Stok::where('barang_id', $barangId)
            ->where('stok_masuk_id', $stokMasukId)
            ->whereNull('stok_keluar_id')->delete();
        return true;
    }

    public static function stokKeluar($barang, $pembayaranId, $tanggal = null)
    {
        $detail = [];
        foreach ($barang as $brg) {
            $stokKeluar = new StokKeluar();
            $stokKeluar->tanggal = $tanggal ?? now();
            $stokKeluar->qty = $brg['qty'];
            $stokKeluar->pembayaran_id = $pembayaranId;
            $stokKeluar->barang_id = $brg['barang_id'];
            $stokKeluar->harga = $brg['harga'];
            $stokKeluar->pengguna_id = auth()->id();
            $stokKeluar->barang_satuan_id = $brg['barang_satuan_id'];
            $stokKeluar->rasio_dari_terkecil = $brg['rasio_dari_terkecil'];
            $stokKeluar->save();

            Stok::where('barang_id', $brg['barang_id'])->available()->orderBy('tanggal_kedaluarsa', 'asc')->limit($brg['qty'] * $brg['rasio_dari_terkecil'])->update([
                'tanggal_keluar' => now(),
                'stok_keluar_id' => $stokKeluar->id,
            ]);

            $hargaBeli = Stok::where('barang_id', $brg['barang_id'])
                ->where('stok_keluar_id', $stokKeluar->id)
                ->sum('harga_beli');

            $detail[] = [
                'kode_akun_id' => $brg['kode_akun_id'],
                'debet' => 0,
                'kredit' => $hargaBeli,
            ];
            $detail[] = [
                'kode_akun_id' => $brg['kode_akun_penjualan_id'],
                'debet' => 0,
                'kredit' => $brg['harga'] * $brg['qty'],
            ];
            $detail[] = [
                'kode_akun_id' => $brg['kode_akun_modal_id'],
                'debet' => $hargaBeli,
                'kredit' => 0,
            ];
        }
        return collect($detail)->groupBy('kode_akun_id')->map(fn($q) => [
            'kode_akun_id' => $q->first()['kode_akun_id'],
            'debet' => $q->sum('debet'),
            'kredit' => $q->sum('kredit'),
        ])->toArray();
    }
}
