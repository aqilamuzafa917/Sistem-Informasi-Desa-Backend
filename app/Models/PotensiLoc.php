<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotensiLoc extends Model
{
    protected $table = 'potensi_loc';

    protected $fillable = [
        'nama',
        'latitude',
        'longitude',
        'alamat',
        'kategori',
        'tags',
        'artikel_id'
    ];

    protected $casts = [
        'nama' => 'string',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'alamat' => 'string',
        'kategori' => 'string',
        'tags' => 'array',
        'artikel_id' => 'integer',
    ];

    public function scopeWithTags($query, array $tags)
    {
        return $query->whereJsonContains('tags', $tags);
    }
    
}
