<?php

namespace Database\Seeders;

use App\Models\TotalApbDesa;
use App\Models\User;
use Illuminate\Database\Seeder;

class TotalApbDesaSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if data already exists
        if (TotalApbDesa::count() > 0) {
            return;
        }

        // Get admin user or skip if not found
        $admin = User::where('email', 'admin1@desa.com')->first();
        if (!$admin) {
            return;
        }

        $adminId = $admin->id;

        // Data APBDesa 2023
        TotalApbDesa::create([
            'tahun_anggaran' => 2023,
            'total_pendapatan' => 500000000,
            'total_belanja' => 450000000,
            'saldo_sisa' => 50000000,
            'tanggal_pelaporan' => '2023-12-31',
            'keterangan' => 'APBDesa Tahun 2023',
            'user_id' => $adminId
        ]);

        // Data APBDesa 2024
        TotalApbDesa::create([
            'tahun_anggaran' => 2024,
            'total_pendapatan' => 550000000,
            'total_belanja' => 480000000,
            'saldo_sisa' => 70000000,
            'tanggal_pelaporan' => '2024-12-31',
            'keterangan' => 'APBDesa Tahun 2024',
            'user_id' => $adminId
        ]);
    }
} 