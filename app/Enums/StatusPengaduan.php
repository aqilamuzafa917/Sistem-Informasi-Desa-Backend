<?php

namespace App\Enums;

enum StatusPengaduan : string
{
    case Diterima = 'Diterima';
    case Diproses = 'Diproses';
    case Selesai = 'Selesai';
    case Ditolak = 'Ditolak';

    public function label(): string
    {
        return match($this) {
            self::Diterima => 'Diajukan',
            self::Diproses => 'Diterima',
            self::Ditolak => 'Ditolak',
        };
    }
}
