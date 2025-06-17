<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorIDM extends Model
{
    protected $table = 'indikator_idm';
    protected $fillable = [
        'nama_indikator',
        'kategori',
    ];

    protected $casts = [
        'nama_indikator' => 'string',
        'kategori' => 'string',
    ];

    public $timestamps = true;

    public function variabelIDM()
    {
        return $this->hasMany(VariabelIDM::class);
    }
}
