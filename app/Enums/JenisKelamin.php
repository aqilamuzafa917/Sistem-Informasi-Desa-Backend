<?php

namespace App\Enums;

enum JenisKelamin : string
{
    case LakiLaki = "Laki-laki";
    case Perempuan = "Perempuan";

    public function label(): string
    {
        return match($this) {
            self::LakiLaki => "Laki-laki",
            self::Perempuan => "Perempuan",
        };
    }
}
