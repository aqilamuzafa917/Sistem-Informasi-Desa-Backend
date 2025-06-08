<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IDM extends Model
{
    protected $table = 'idm';
    protected $fillable = [
        'tahun',
        'skor_idm',
        'status_idm',
        'target_status',
        'skor_minimal',
        'penambahan',
        'komponen'
    ];

    public $timestamps = true;

    protected $casts = [
        'tahun' => 'integer',
        'skor_idm' => 'float',
        'status_idm' => 'string',
        'target_status' => 'string',
        'skor_minimal' => 'float',
        'penambahan' => 'float',
        'komponen' => 'json',
    ];
}
