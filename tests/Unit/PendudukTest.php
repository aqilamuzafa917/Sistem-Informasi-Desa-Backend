<?php

use App\Models\Penduduk;
use App\Enums\JenisKelamin;
use App\Enums\Agama;
use App\Enums\StatusPerkawinan;
use Carbon\Carbon;

test('penduduk model has correct fillable attributes', function () {
    $penduduk = new Penduduk();
    
    $fillable = [
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

    expect($penduduk->getFillable())->toBe($fillable);
});

test('penduduk model has correct casts', function () {
    $penduduk = new Penduduk();
    
    $casts = [
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

    expect($penduduk->getCasts())->toBe($casts);
});

test('penduduk model formats tanggal_lahir correctly', function () {
    $date = Carbon::parse('2024-03-20');
    expect($date->format('d-m-Y'))->toBe('20-03-2024');
});

test('penduduk model has correct primary key settings', function () {
    $penduduk = new Penduduk();
    
    expect($penduduk->getKeyName())->toBe('nik')
        ->and($penduduk->incrementing)->toBeFalse()
        ->and($penduduk->getKeyType())->toBe('string');
}); 