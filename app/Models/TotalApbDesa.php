<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalApbDesa extends Model
{
    use HasFactory;

    protected $table = 'total_apb_desa';
    protected $primaryKey = 'id_total';
    
    protected $fillable = [
        'tahun_anggaran',
        'total_pendapatan',
        'total_belanja',
        'saldo_sisa',
        'tanggal_pelaporan',
        'keterangan',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}