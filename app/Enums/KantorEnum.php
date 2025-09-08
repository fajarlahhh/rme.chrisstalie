<?php

namespace App\Enums;

enum KantorEnum: String
{
    //
    case Apotek = "Apotek";
    case Klinik = "Klinik";

    public function label(): string
    {
        return match ($this) {
            self::Apotek => 'Apotek',
            self::Klinik => 'Klinik',
        };
    }
}
