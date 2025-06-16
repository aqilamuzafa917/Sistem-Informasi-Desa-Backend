<?php

namespace Database\Seeders;

use App\Models\RealisasiPendapatan;
use App\Models\User;
use Illuminate\Database\Seeder;

class RealisasiPendapatanSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if data already exists
        if (RealisasiPendapatan::count() > 0) {
            return;
        }

        // Get admin user or skip if not found
        $admin = User::where('email', 'admin1@desa.com')->first();
        if (!$admin) {
            return;
        }

        $adminId = $admin->id;

        // Data Pendapatan 2023
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-01-15',
            'kategori' => 'Pendapatan Asli Desa',
            'sub_kategori' => 'Hasil Usaha',
            'deskripsi' => 'Pendapatan dari Pasar Desa',
            'jumlah' => 75000000,
            'sumber_dana' => 'Pasar Desa Sukamaju',
            'keterangan' => 'Pendapatan sewa kios pasar desa',
            'user_id' => $adminId
        ]);

        RealisasiPendapatan::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-02-20',
            'kategori' => 'Pendapatan Transfer',
            'sub_kategori' => 'Dana Desa',
            'deskripsi' => 'Dana Desa Tahap 1',
            'jumlah' => 250000000,
            'sumber_dana' => 'Pemerintah Pusat',
            'keterangan' => 'Transfer dana desa tahap 1 tahun 2023',
            'user_id' => $adminId
        ]);

        // Data Pendapatan 2024
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-01-15',
            'kategori' => 'Pendapatan Asli Desa',
            'sub_kategori' => 'Hasil Usaha',
            'deskripsi' => 'Pendapatan dari Pasar Desa',
            'jumlah' => 85000000,
            'sumber_dana' => 'Pasar Desa Sukamaju',
            'keterangan' => 'Pendapatan sewa kios pasar desa',
            'user_id' => $adminId
        ]);

        RealisasiPendapatan::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-02-20',
            'kategori' => 'Pendapatan Transfer',
            'sub_kategori' => 'Dana Desa',
            'deskripsi' => 'Dana Desa Tahap 1',
            'jumlah' => 275000000,
            'sumber_dana' => 'Pemerintah Pusat',
            'keterangan' => 'Transfer dana desa tahap 1 tahun 2024',
            'user_id' => $adminId
        ]);
    }
} 