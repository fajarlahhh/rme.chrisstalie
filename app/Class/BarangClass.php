<?php

namespace App\Class;

use App\Models\Barang;

class BarangClass
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function getBarang($persediaan = null)
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
}
