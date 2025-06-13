<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorIDM extends Model
{
    protected $table = 'indikator_idm';
    protected $fillable = [
        'nama_indikator',
    ];

    protected $casts = [
        'nama_indikator' => 'string',
    ];

    public $timestamps = false;

    public function variabelIDM()
    {
        return $this->hasMany(VariabelIDM::class);
    }
}
