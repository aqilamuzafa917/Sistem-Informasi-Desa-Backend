<?php

namespace App\Models;

use App\Enums\KategoriPengaduan;
use App\Enums\StatusPengaduan;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';

    protected $fillable = [
        'nama',
        'nomor_telepon',
        'kategori',
        'detail_pengaduan',
        'status',
        'media',
    ];

    protected $keyType = 'string';

    protected $casts = [
        'nama' => 'string',
        'nomor_telepon' => 'string',
        'kategori' => KategoriPengaduan::class,
        'detail_pengaduan' => 'string',
        'status' => StatusPengaduan::class,
        'media' => 'array',
    ];
}
