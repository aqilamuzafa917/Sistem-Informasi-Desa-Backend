<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VariabelIdmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variabels = [
            [
                'indikator_idm' => 'Skor Akses SMP/MTS',
                'skor' => 3,
                'keterangan' => 'Mayoritas warga lulusan SMP',
                'kegiatan' => 'Pendidikan nonformal dan beasiswa',
                'nilai_plus' => 0.3,
                'pelaksana' => json_encode(['PKBM', 'Karang Taruna']),
                'kategori' => 'IKS',
                'tahun' => 2024,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'indikator_idm' => 'Skor Aktivitas Posyandu',
                'skor' => 4,
                'keterangan' => 'Tersedia di setiap dusun',
                'kegiatan' => 'Peningkatan kapasitas kader Posyandu',
                'nilai_plus' => 0.4,
                'pelaksana' => json_encode(['Desa', 'Puskesmas']),
                'kategori' => 'IKS',
                'tahun' => 2024,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'indikator_idm' => 'Skor Air Minum',
                'skor' => 4,
                'keterangan' => 'Sudah tersedia PDAM di mayoritas rumah',
                'kegiatan' => 'Pembangunan jaringan air bersih',
                'nilai_plus' => 0.5,
                'pelaksana' => json_encode(['Desa', 'Masyarakat']),
                'kategori' => 'IKS',
                'tahun' => 2024,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'indikator_idm' => 'Skor Akses Internet Warga',
                'skor' => 2,
                'keterangan' => 'Hanya sebagian warga yang memiliki akses internet',
                'kegiatan' => 'Pembangunan jaringan wifi publik',
                'nilai_plus' => 0.2,
                'pelaksana' => json_encode(['Desa', 'BUMDes']),
                'kategori' => 'IKS',
                'tahun' => 2024,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'indikator_idm' => 'Skor Kualitas Lingkungan',
                'skor' => 5,
                'keterangan' => 'Lingkungan bersih dan bebas pencemaran',
                'kegiatan' => 'Program bank sampah & penghijauan',
                'nilai_plus' => 0.6,
                'pelaksana' => json_encode(['Karang Taruna', 'Masyarakat']),
                'kategori' => 'IKL',
                'tahun' => 2024,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('variabel_idm')->insertOrIgnore($variabels);
    }
} 