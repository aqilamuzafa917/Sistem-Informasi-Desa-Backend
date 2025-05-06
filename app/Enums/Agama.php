<?php

namespace App\Enums;

enum Agama : string
{
    case Islam = 'Islam';
    case Kristen = 'Kristen';
    case Katolik = 'Katolik';
    case Hindu = 'Hindu';
    case Buddha = 'Buddha';
    case Konghucu = 'Konghucu';

    public function label(): string
    {
        return match($this) {
            self::Islam => 'Islam',
            self::Kristen => 'Kristen',
            self::Katolik => 'Katolik',
            self::Hindu => 'Hindu',
            self::Buddha => 'Buddha',
            self::Konghucu => 'Konghucu',
        };
    }
}
{
    //
}
