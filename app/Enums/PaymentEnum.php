<?php

namespace App\Enums;

enum PaymentEnum: String
{
    //
    case Cash = "Cash";
    case QRIS = "QRIS";
    case BCA = "BCA";
    case Mandiri = "Mandiri";

    public function label(): string
    {
        return match ($this) {
            self::Mandiri => 'Mandiri',
            self::Cash => 'Cash',
            self::QRIS => 'QRIS',
            self::BCA => 'BCA',
        };
    }
}
