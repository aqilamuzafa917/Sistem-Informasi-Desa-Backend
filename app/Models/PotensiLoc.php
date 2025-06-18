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
        'kategori',
        'tags'
    ];

    protected $casts = [
        'nama' => 'string',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'kategori' => 'string',
        'tags' => 'array'
    ];

    public function scopeWithTags($query, array $tags)
    {
        return $query->whereJsonContains('tags', $tags);
    }
    
    public function artikel()
    {
        return $this->hasMany(Artikel::class, 'potensi_id');
    }
}
