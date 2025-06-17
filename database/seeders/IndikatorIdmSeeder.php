<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndikatorIdmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indikators = [
            ['nama_indikator' => 'Skor Akses Sarkes', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Dokter', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Bidan', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Nakes Lain', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Tingkat Kepesertaan BPJS', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Akses Poskesdes', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Aktivitas Posyandu', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Akses SD/MI', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Akses SMP/MTS', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Akses SMA/SMK', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Ketersediaan PAUD', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Ketersediaan PKBM/Paket ABC', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Ketersediaan Kursus', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Ketersediaan Taman Baca/Perpus Desa', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Kebiasaan Goryong', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Frekuensi Goryong', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Ketersediaan Ruang Publik', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Kelompok OR', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Kegiatan OR', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Keragaman Agama', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Keragaman Bahasa', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Keragaman Komunikasi', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Poskamling', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Siskamling', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Konflik', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor PMKS', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor SLB', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Akses Listrik', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Sinyal Tlp', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Internet Kantor Desa', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Akses Internet Warga', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Akses Jamban', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Sampah', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Air Minum', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Air Mandi & Cuci', 'kategori' => 'IKS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Keragaman Produksi', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Pertokoan', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Pasar', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Toko/Warung Kelontong', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Kedai & Penginapan', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor POS & Logistik', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Bank & BPR', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Kredit', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Lembaga Ekonomi', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Moda Transportasi Umum', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Keterbukaan Wilayah', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Kualitas Jalan', 'kategori' => 'IKE', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Kualitas Lingkungan', 'kategori' => 'IKL', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Rawan Bencana', 'kategori' => 'IKL', 'created_at' => now(), 'updated_at' => now()],
            ['nama_indikator' => 'Skor Tanggap Bencana', 'kategori' => 'IKL', 'created_at' => now(), 'updated_at' => now()]
        ];

        DB::table('indikator_idm')->insertOrIgnore($indikators);
    }
} 