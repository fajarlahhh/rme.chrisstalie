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

    public static function getBarang($persediaan = null, $khusus = null)
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

    public static function stokKeluar($barang, $pembayaranId, $jenis)
    {
        $detail = [];
        foreach ($barang as $brg) {
            $stokKeluarId = Str::uuid();
            StokKeluar::insert([
                'id' => $stokKeluarId,
                'tanggal' => now(),
                'qty' => $brg['qty'],
                'pembayaran_id' => $pembayaranId,
                'barang_id' => $brg['barang_id'],
                'harga' => $brg['harga'],
                'pengguna_id' => auth()->id(),
                'barang_satuan_id' => $brg['barang_satuan_id'],
                'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Stok::where('barang_id', $brg['barang_id'])->available()->orderBy('tanggal_kedaluarsa', 'asc')->limit($brg['qty'] * $brg['rasio_dari_terkecil'])->update([
                'tanggal_keluar' => now(),
                'stok_keluar_id' => $stokKeluarId,
            ]);

            $id = Str::uuid();
            $hargaBeli = Stok::where('barang_id', $brg['barang_id'])
                ->where('stok_keluar_id', $stokKeluarId)
                ->sum('harga_beli');

            $detail[] = [
                'jurnal_id' => $id,
                'kode_akun_id' => $brg['kode_akun_id'],
                'debet' => 0,
                'kredit' => $hargaBeli,
            ];
            $detail[] = [
                'jurnal_id' => $id,
                'kode_akun_id' => $brg['kode_akun_modal_id'],
                'debet' => $hargaBeli,
                'kredit' => 0,
            ];

            JurnalClass::insert($id, $jenis, [
                'tanggal' => now(),
                'uraian' => 'Modal ' . strtolower($jenis) . ' ' . $pembayaranId,
                'referensi_id' => $pembayaranId,
                'pengguna_id' => auth()->id(),
            ], $detail);

        }
    }
}
