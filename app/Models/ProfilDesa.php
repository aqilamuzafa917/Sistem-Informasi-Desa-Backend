<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilDesa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'profil_desas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_desa',
        'sejarah',
        'tradisi_budaya',
        'visi',
        'misi',
        'peta_lokasi',
        'alamat_kantor',
        'struktur_organisasi',
        'batas_wilayah', // Add this line
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'struktur_organisasi' => 'array',
        'batas_wilayah' => 'array', // Add this line to cast GeoJSON/Polygon to array and vice versa
    ];
}
