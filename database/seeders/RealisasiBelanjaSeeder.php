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
        // Belanja Barang/Jasa
        RealisasiBelanja::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-03-15',
            'kategori' => 'Belanja Barang/Jasa',
            'deskripsi' => 'Pembelian Peralatan Kantor',
            'jumlah' => 25000000,
            'penerima_vendor' => 'Toko Sinar Jaya',
            'keterangan' => 'Pembelian komputer dan printer untuk kantor desa',
            'user_id' => $adminId
        ]);

        // Belanja Modal
        RealisasiBelanja::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-06-20',
            'kategori' => 'Belanja Modal',
            'deskripsi' => 'Pembangunan Jalan Desa',
            'jumlah' => 150000000,
            'penerima_vendor' => 'PT Jaya Konstruksi',
            'keterangan' => 'Pembangunan jalan desa sepanjang 2 km',
            'user_id' => $adminId
        ]);

        // Belanja Tak Terduga
        RealisasiBelanja::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-12-10',
            'kategori' => 'Belanja Tak Terduga',
            'deskripsi' => 'Bantuan Bencana Alam',
            'jumlah' => 50000000,
            'penerima_vendor' => 'Badan Penanggulangan Bencana',
            'keterangan' => 'Bantuan untuk korban banjir',
            'user_id' => $adminId
        ]);

        // Data Belanja 2023
        // Belanja Barang/Jasa - ATK
        RealisasiBelanja::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-08-10',
            'kategori' => 'Belanja Barang/Jasa',
            'deskripsi' => 'Pembelian Kertas HVS',
            'jumlah' => 450000,
            'penerima_vendor' => 'Toko Gramedia',
            'keterangan' => 'Pembelian Kertas HVS untuk keperluan administrasi desa',
            'user_id' => $adminId
        ]);

        // Belanja Modal - Peralatan
        RealisasiBelanja::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-08-15',
            'kategori' => 'Belanja Modal',
            'deskripsi' => 'Pembelian Komputer Kantor',
            'jumlah' => 7000000,
            'penerima_vendor' => 'CV. Teknologi Maju',
            'keterangan' => 'Pengadaan komputer baru untuk staff',
            'user_id' => $adminId
        ]);

        // Belanja Tak Terduga - Bencana
        RealisasiBelanja::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-08-20',
            'kategori' => 'Belanja Tak Terduga',
            'deskripsi' => 'Bantuan Bencana Alam',
            'jumlah' => 1500000,
            'penerima_vendor' => 'Warga Terdampak',
            'keterangan' => 'Bantuan untuk korban banjir bandang',
            'user_id' => $adminId
        ]);

        // Data Belanja 2024
        // Belanja Barang/Jasa - ATK
        RealisasiBelanja::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-07-10',
            'kategori' => 'Belanja Barang/Jasa',
            'deskripsi' => 'Pembelian Kertas HVS',
            'jumlah' => 500000,
            'penerima_vendor' => 'Toko Gramedia',
            'keterangan' => 'Pembelian Kertas HVS untuk keperluan administrasi desa',
            'user_id' => $adminId
        ]);

        // Belanja Modal - Peralatan
        RealisasiBelanja::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-07-11',
            'kategori' => 'Belanja Modal',
            'deskripsi' => 'Pembelian Komputer Kantor',
            'jumlah' => 7500000,
            'penerima_vendor' => 'CV. Teknologi Maju',
            'keterangan' => 'Pengadaan komputer baru untuk staff',
            'user_id' => $adminId
        ]);

        // Belanja Tak Terduga - Bencana
        RealisasiBelanja::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-07-12',
            'kategori' => 'Belanja Tak Terduga',
            'deskripsi' => 'Bantuan Bencana Alam',
            'jumlah' => 2000000,
            'penerima_vendor' => 'Warga Terdampak',
            'keterangan' => 'Bantuan untuk korban banjir bandang',
            'user_id' => $adminId
        ]);

        // Belanja Barang/Jasa - Internet
        RealisasiBelanja::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-01-15',
            'kategori' => 'Belanja Barang/Jasa',
            'deskripsi' => 'Langganan Internet Kantor',
            'jumlah' => 350000,
            'penerima_vendor' => 'PT. Telkom Indonesia',
            'keterangan' => 'Pembayaran langganan internet bulanan',
            'user_id' => $adminId
        ]);

        // Data Belanja 2025
        // Belanja Barang/Jasa - ATK
        RealisasiBelanja::create([
            'tahun_anggaran' => 2025,
            'tanggal_realisasi' => '2025-08-05',
            'kategori' => 'Belanja Barang/Jasa',
            'deskripsi' => 'Pembelian Kertas HVS',
            'jumlah' => 550000,
            'penerima_vendor' => 'Toko Gramedia',
            'keterangan' => 'Pembelian Kertas HVS untuk keperluan administrasi desa',
            'user_id' => $adminId
        ]);

        // Belanja Modal - Peralatan
        RealisasiBelanja::create([
            'tahun_anggaran' => 2025,
            'tanggal_realisasi' => '2025-08-10',
            'kategori' => 'Belanja Modal',
            'deskripsi' => 'Pembelian Komputer Kantor',
            'jumlah' => 8000000,
            'penerima_vendor' => 'CV. Teknologi Maju',
            'keterangan' => 'Pengadaan komputer baru untuk staff',
            'user_id' => $adminId
        ]);

        // Belanja Tak Terduga - Bencana
        RealisasiBelanja::create([
            'tahun_anggaran' => 2025,
            'tanggal_realisasi' => '2025-08-15',
            'kategori' => 'Belanja Tak Terduga',
            'deskripsi' => 'Bantuan Bencana Alam',
            'jumlah' => 2500000,
            'penerima_vendor' => 'Warga Terdampak',
            'keterangan' => 'Bantuan untuk korban banjir bandang',
            'user_id' => $adminId
        ]);
    }
} 