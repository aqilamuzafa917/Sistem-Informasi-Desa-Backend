<?php

namespace App\Enums;

enum KategoriPotensi : string
{
    case Sekolah = 'sekolah';
    case Tempat_Ibadah = 'tempat_ibadah';
    case Kesehatan = 'kesehatan';
    case Fasilitas_Lainnya = 'fasilitas_lainnya';
    case Pertanian = 'pertanian';
    Case Peternakan = 'peternakan';
    case Industri = 'industri';
    case Wisata = 'wisata';

    public function label(): string
    {
        return match ($this) {
            self::Sekolah => 'Sekolah',
            self::Tempat_Ibadah => 'Tempat Ibadah',
            self::Kesehatan => 'Kesehatan',
            self::Fasilitas_Lainnya => 'Fasilitas Lainnya',
            self::Pertanian => 'Pertanian',
            self::Peternakan => 'Peternakan',
            self::Industri => 'Industri',
            self::Wisata => 'Wisata',
        };
    }

}
