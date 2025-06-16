<?php

namespace Database\Seeders;

use App\Models\RealisasiBelanja;
use App\Models\User;
use Illuminate\Database\Seeder;

class RealisasiBelanjaSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if data already exists
        if (RealisasiBelanja::count() > 0) {
            return;
        }

        // Get admin user or skip if not found
        $admin = User::where('email', 'admin1@desa.com')->first();
        if (!$admin) {
            return;
        }

        $adminId = $admin->id;

        // Data Belanja 2023
        RealisasiBelanja::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-03-15',
            'kategori' => 'Belanja Modal',
            'deskripsi' => 'Pembangunan Jalan Desa',
            'jumlah' => 150000000,
            'penerima_vendor' => 'PT Jaya Konstruksi',
            'keterangan' => 'Pembangunan jalan desa sepanjang 2 km',
            'user_id' => $adminId
        ]);

        RealisasiBelanja::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-06-20',
            'kategori' => 'Belanja Barang/Jasa',
            'deskripsi' => 'Pembelian Peralatan Kantor',
            'jumlah' => 25000000,
            'penerima_vendor' => 'Toko Sinar Jaya',
            'keterangan' => 'Pembelian komputer dan printer untuk kantor desa',
            'user_id' => $adminId
        ]);

        // Data Belanja 2024
        RealisasiBelanja::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-02-10',
            'kategori' => 'Belanja Modal',
            'deskripsi' => 'Pembangunan Drainase',
            'jumlah' => 180000000,
            'penerima_vendor' => 'CV Maju Bersama',
            'keterangan' => 'Pembangunan drainase sepanjang 1.5 km',
            'user_id' => $adminId
        ]);

        RealisasiBelanja::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-04-05',
            'kategori' => 'Belanja Barang/Jasa',
            'deskripsi' => 'Pembelian ATK',
            'jumlah' => 15000000,
            'penerima_vendor' => 'Toko Sinar Jaya',
            'keterangan' => 'Pembelian alat tulis kantor untuk 6 bulan',
            'user_id' => $adminId
        ]);
    }
} 