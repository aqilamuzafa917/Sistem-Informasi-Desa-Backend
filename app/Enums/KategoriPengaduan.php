<?php

namespace App\Enums;

enum KategoriPengaduan : string
{
    case Umum = 'Umum';
    case Sosial = 'Sosial';
    case Keamanan = 'Keamanan';
    case Kesehatan = 'Kesehatan';
    case Kebersihan = 'Kebersihan';
    case Permintaan = 'Permintaan';

    public function label(): string
    {
        return match($this) {
            self::Umum => 'Umum',
            self::Sosial => 'Sosial',
            self::Keamanan => 'Keamanan',
            self::Kesehatan => 'Kesehatan',
            self::Kebersihan => 'Kebersihan',
            self::Permintaan => 'Permintaan',
        };
    }

}
