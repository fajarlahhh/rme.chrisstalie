<?php

namespace App\Enums;

enum JenisBarangEnum: String
{
    //
    case Obat = "Obat";
    case AlatKesehatan = "Alat Kesehatan";
    case ProdukKecantikan = "Produk Kecantikan";

    public function label(): string
    {
        return match ($this) {
            self::Obat => 'Obat',
            self::AlatKesehatan => 'Alat Kesehatan',
            self::ProdukKecantikan => 'Produk Kecantikan',
        };
    }
}
