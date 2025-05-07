<?php

namespace App\Enums;

enum StatusPerkawinan : string
{
    case BelumMenikah = 'Belum Menikah';
    case Menikah = 'Menikah';
    case CeraiHidup = 'Cerai Hidup';
    case CeraiMati = 'Cerai Mati';

    public function label(): string
    {
        return match($this) {
            self::BelumMenikah => 'Belum Menikah',
            self::Menikah => 'Menikah',
            self::CeraiHidup => 'Cerai Hidup',
            self::CeraiMati => 'Cerai Mati',
        };
    }
}
