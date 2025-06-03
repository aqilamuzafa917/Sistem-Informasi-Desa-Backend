<?php

namespace App\Enums;

enum StatusPengaduan : string
{
    case Diajukan = 'Diajukan';
    case Diterima = 'Diterima';
    case Ditolak = 'Ditolak';

    public function label(): string
    {
        return match($this) {
            self::Diajukan => 'Diajukan',
            self::Diterima => 'Diterima',
            self::Ditolak => 'Ditolak',
        };
    }
}
