<?php

namespace App\Models;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\StatusPerkawinan;
use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    protected $table = 'penduduk';

    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'rt',
        'rw',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'kewarganegaraan',
        'pendidikan',
        'no_kk',
    ];

    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'nik' => 'string',
        'nama' => 'string',
        'tempat_lahir' => 'string',
        'tanggal_lahir' => 'date',
        'jenis_kelamin' => JenisKelamin::class,
        'alamat' => 'string',
        'rt' => 'string',
        'rw' => 'string',
        'desa_kelurahan' => 'string',
        'kecamatan' => 'string',
        'kabupaten_kota' => 'string',
        'provinsi' => 'string',
        'kode_pos' => 'string',
        'agama' => Agama::class,
        'status_perkawinan' => StatusPerkawinan::class,
        'pekerjaan' => 'string',
        'kewarganegaraan' => 'string',
        'pendidikan' => 'string',
        'no_kk' => 'string',
    ];
    
    public function getTanggalLahirAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }
}
