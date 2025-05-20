<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiPendapatan extends Model
{
    use HasFactory;

    protected $table = 'realisasi_pendapatan';
    protected $primaryKey = 'id_pendapatan';
    
    protected $fillable = [
        'tahun_anggaran',
        'tanggal_realisasi',
        'kategori',
        'sub_kategori',
        'deskripsi',
        'jumlah',
        'sumber_dana',
        'keterangan',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}