<?php

namespace App\Enums;

enum KategoriVariabelIDM: string
{
    case IKL = 'IKL';
    case IKE = 'IKE';
    case IKS = 'IKS';

    public function label(): string
    {
        return match($this) {
            self::IKL => 'Indeks Kualitas Lingkungan',
            self::IKE => 'Indeks Kualitas Ekonomi',
            self::IKS => 'Indeks Kualitas Sosial',
        };
    }
}
