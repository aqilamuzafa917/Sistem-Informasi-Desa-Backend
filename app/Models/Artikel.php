<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'artikels';

    /**
     * Primary key tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_artikel';

    /**
     * Atribut yang dapat diisi.
     *
     * @var array
     */
    protected $fillable = [
        'jenis_artikel',
        'status_artikel',
        'judul_artikel',
        'kategori_artikel',
        'isi_artikel',
        'penulis_artikel',
        'tanggal_kejadian_artikel',
        'tanggal_publikasi_artikel',
        'latitude',
        'longitude',
        'location_name',
        'media_artikel',
    ];

    /**
     * Atribut yang harus dikonversi.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_kejadian_artikel' => 'date',
        'tanggal_publikasi_artikel' => 'datetime',
        'media_artikel' => 'json',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Boot method untuk menambahkan event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Event saat artikel baru dibuat
        static::creating(function ($artikel) {
            // Jika jenis artikel adalah resmi, maka status langsung disetujui
            if ($artikel->jenis_artikel === 'resmi') {
                $artikel->status_artikel = 'disetujui';
                $artikel->tanggal_publikasi_artikel = now();
            } else {
                // Jika jenis artikel adalah warga, maka status diajukan
                $artikel->status_artikel = 'diajukan';
                $artikel->tanggal_publikasi_artikel = null;
            }
        });

        // Event saat artikel diupdate
        static::updating(function ($artikel) {
            // Jika status berubah menjadi disetujui dan tanggal publikasi belum ada
            if ($artikel->isDirty('status_artikel') && 
                $artikel->status_artikel === 'disetujui' && 
                !$artikel->tanggal_publikasi_artikel) {
                $artikel->tanggal_publikasi_artikel = now();
            }
        });
    }

}