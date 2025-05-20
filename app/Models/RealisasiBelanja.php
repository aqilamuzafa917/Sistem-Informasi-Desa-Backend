<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiBelanja extends Model
{
    use HasFactory;

    protected $table = 'realisasi_belanja';
    protected $primaryKey = 'id_belanja';
    
    protected $fillable = [
        'tahun_anggaran',
        'tanggal_realisasi',
        'kategori',
        'deskripsi',
        'jumlah',
        'penerima_vendor',
        'keterangan',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}