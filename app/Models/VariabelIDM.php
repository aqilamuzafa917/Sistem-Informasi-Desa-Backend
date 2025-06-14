<?php

namespace App\Models;

use App\Enums\KategoriVariabelIDM;
use Illuminate\Database\Eloquent\Model;

class VariabelIDM extends Model
{
    protected $table = 'variabel_idm';
    protected $fillable = [
        'indikator_idm',
        'skor',
        'keterangan',
        'kegiatan',
        'nilai_plus',
        'pelaksana',
        'kategori',
        'tahun'
    ];

    protected $casts = [
        'indikator_idm' => 'string',
        'skor' => 'integer',
        'keterangan' => 'string',
        'kegiatan' => 'string',
        'nilai_plus' => 'float',
        'pelaksana' => 'array',
        'kategori' => KategoriVariabelIDM::class,
        'tahun' => 'integer'
    ];

    public function indikator()
    {
        return $this->belongsTo(IndikatorIDM::class, 'nama_indikator', 'indikator_idm');
    }
}
