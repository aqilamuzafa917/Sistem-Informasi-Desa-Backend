<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'nama',
        'nik',
        'jenis_surat',
        'keperluan',
        'status',
        'kode_surat',
        'kode_desa',
        'catatan_admin', 
    ];

    public static function generateNomorSurat()
    {
        // Format nomor surat: 045/DS.2012/IV/2019
        $kodeSurat = '045'; // Kode unik surat (bisa dibuat dinamis)
        $kodeDesa = 'DS.2012'; // Kode desa (bisa diambil dari database)
        $bulanRomawi = self::getBulanRomawi(date('m'));
        $tahun = date('Y');

        // Hitung jumlah surat yang sudah dibuat bulan ini
        $count = self::whereYear('created_at', $tahun)
                    ->whereMonth('created_at', date('m'))
                    ->count() + 1; // Nomor urut surat

        // Format final
        return sprintf('%03d/%s/%s/%d', $count, $kodeDesa, $bulanRomawi, $tahun);
    }

    private static function getBulanRomawi($bulan)
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romawi[intval($bulan)];
    }
}
