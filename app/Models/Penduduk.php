<?php

namespace App\Models;

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
    ];

    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'nik' => 'string',
        'nama' => 'string',
        'tempat_lahir' => 'string',
        'tanggal_lahir' => 'date',
        'jenis_kelamin' => 'enum: Laki-laki, Perempuan',
        'alamat' => 'string',
        'rt' => 'string',
        'rw' => 'string',
        'desa_kelurahan' => 'string',
        'kecamatan' => 'string',
        'kabupaten_kota' => 'string',
        'provinsi' => 'string',
        'kode_pos' => 'string',
        'agama' => 'enum: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu',
        'status_perkawinan' => 'enum: Belum Menikah, Menikah, Cerai Hidup, Cerai Mati',
        'pekerjaan' => 'string',
        'kewarganegaraan' => 'string',
    ];
    

    public function getTanggalLahirAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }
}
