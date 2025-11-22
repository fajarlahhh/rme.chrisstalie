<?php

namespace App\Class;

use App\Models\Stok;
use App\Models\StokMasuk;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StokClass
{

    public static function insert($data)
    {
        DB::transaction(function () use ($data) {
            $stok = [];
            $stokMasuk = new StokMasuk();
            $stokMasuk->qty = $data['qty'];
            $stokMasuk->no_batch = $data['no_batch'];
            $stokMasuk->tanggal_kedaluarsa = $data['tanggal_kedaluarsa'];
            $stokMasuk->barang_id = $data['barang_id'];
            $stokMasuk->pembelian_id = $data['pembelian_id'];
            $stokMasuk->barang_satuan_id = $data['barang_satuan_id'];
            $stokMasuk->rasio_dari_terkecil = $data['rasio_dari_terkecil'];
            $stokMasuk->pengguna_id = auth()->id();
            $stokMasuk->created_at = now();
            $stokMasuk->updated_at = now();
            $stokMasuk->save();

            for ($i = 0; $i < $data['rasio_dari_terkecil'] * $data['qty']; $i++) {
                $stok[] = [
                    'id' => Str::uuid(),
                    'pembelian_id' => $data['pembelian_id'],
                    'barang_id' => $data['barang_id'],
                    'no_batch' => $data['no_batch'],
                    'tanggal_kedaluarsa' => $data['tanggal_kedaluarsa'],
                    'stok_masuk_id' => $stokMasuk->id,
                    'tanggal_masuk' => now(),
                    'harga_beli' => $data['harga_beli'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            foreach (array_chunk($stok, 1000) as $chunk) {
                Stok::insert($chunk);
            }
        });
    }
}
