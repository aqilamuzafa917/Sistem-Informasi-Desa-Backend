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
        // Pendapatan Asli Desa
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

        // Pendapatan Transfer
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

        // Pendapatan Lain-lain
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-03-15',
            'kategori' => 'Pendapatan Lain-lain',
            'sub_kategori' => 'Hibah',
            'deskripsi' => 'Hibah dari Perusahaan',
            'jumlah' => 50000000,
            'sumber_dana' => 'PT Maju Bersama',
            'keterangan' => 'Hibah untuk pembangunan desa',
            'user_id' => $adminId
        ]);

        // Data Pendapatan 2024
        // Pendapatan Asli Desa
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

        // Pendapatan Transfer
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

        // Pendapatan Lain-lain
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-03-15',
            'kategori' => 'Pendapatan Lain-lain',
            'sub_kategori' => 'Hibah',
            'deskripsi' => 'Hibah dari Perusahaan',
            'jumlah' => 60000000,
            'sumber_dana' => 'PT Maju Bersama',
            'keterangan' => 'Hibah untuk pembangunan desa',
            'user_id' => $adminId
        ]);

        // Data Pendapatan 2025
        // Pendapatan Asli Desa - Hasil Usaha
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2025,
            'tanggal_realisasi' => '2025-08-01',
            'kategori' => 'Pendapatan Asli Desa',
            'sub_kategori' => 'Hasil Usaha',
            'deskripsi' => 'Keuntungan BUMDes Unit Simpan Pinjam',
            'jumlah' => 2500000,
            'sumber_dana' => 'BUMDes Amanah',
            'keterangan' => 'Laba bersih unit simpan pinjam bulan Juli 2025',
            'user_id' => $adminId
        ]);

        // Pendapatan Asli Desa - Swadaya
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2025,
            'tanggal_realisasi' => '2025-08-05',
            'kategori' => 'Pendapatan Asli Desa',
            'sub_kategori' => 'Swadaya, Partisipasi dan Gotong Royong',
            'deskripsi' => 'Sumbangan Pembangunan Jalan Desa',
            'jumlah' => 5000000,
            'sumber_dana' => 'Masyarakat Dusun Makmur',
            'keterangan' => 'Pengumpulan dana swadaya untuk perbaikan jalan',
            'user_id' => $adminId
        ]);

        // Pendapatan Asli Desa - Lain-lain
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-08-10',
            'kategori' => 'Pendapatan Asli Desa',
            'sub_kategori' => 'Lain-lain Pendapatan Asli Desa yang sah',
            'deskripsi' => 'Retribusi Pasar Desa',
            'jumlah' => 750000,
            'sumber_dana' => 'Pedagang Pasar Desa',
            'keterangan' => 'Penerimaan retribusi mingguan dari pasar desa',
            'user_id' => $adminId
        ]);

        // Pendapatan Transfer - Dana Desa
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-08-15',
            'kategori' => 'Pendapatan Transfer',
            'sub_kategori' => 'Dana Desa',
            'deskripsi' => 'Pencairan Dana Desa Tahap II',
            'jumlah' => 300000000,
            'sumber_dana' => 'APBN',
            'keterangan' => 'Alokasi Dana Desa tahap kedua tahun 2024',
            'user_id' => $adminId
        ]);

        // Pendapatan Transfer - Bagi Hasil Pajak
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-08-20',
            'kategori' => 'Pendapatan Transfer',
            'sub_kategori' => 'Bagian dari hasil pajak & retribusi daerah kabupaten/kota',
            'deskripsi' => 'Bagi Hasil PBB',
            'jumlah' => 15000000,
            'sumber_dana' => 'Pemerintah Kabupaten',
            'keterangan' => 'Penerimaan bagi hasil Pajak Bumi dan Bangunan',
            'user_id' => $adminId
        ]);

        // Pendapatan Transfer - ADD
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2024,
            'tanggal_realisasi' => '2024-08-25',
            'kategori' => 'Pendapatan Transfer',
            'sub_kategori' => 'Alokasi Dana Desa',
            'deskripsi' => 'Penerimaan ADD Bulan Agustus',
            'jumlah' => 70000000,
            'sumber_dana' => 'APBD Kabupaten',
            'keterangan' => 'Alokasi Dana Desa rutin bulanan',
            'user_id' => $adminId
        ]);

        // Pendapatan Transfer - Bantuan Provinsi
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-09-01',
            'kategori' => 'Pendapatan Transfer',
            'sub_kategori' => 'Bantuan Keuangan',
            'deskripsi' => 'Bantuan Keuangan Provinsi untuk Infrastruktur',
            'jumlah' => 100000000,
            'sumber_dana' => 'Pemerintah Provinsi',
            'keterangan' => 'Bantuan khusus untuk pembangunan jembatan',
            'user_id' => $adminId
        ]);

        // Pendapatan Transfer - Bantuan Provinsi PKK
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-09-02',
            'kategori' => 'Pendapatan Transfer',
            'sub_kategori' => 'Bantuan Provinsi',
            'deskripsi' => 'Bantuan Provinsi untuk Program PKK',
            'jumlah' => 10000000,
            'sumber_dana' => 'Pemerintah Provinsi',
            'keterangan' => 'Bantuan untuk kegiatan PKK Desa',
            'user_id' => $adminId
        ]);

        // Pendapatan Transfer - Bantuan Kabupaten
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-09-05',
            'kategori' => 'Pendapatan Transfer',
            'sub_kategori' => 'Bantuan Kabupaten / Kota',
            'deskripsi' => 'Bantuan Kabupaten untuk Sarana Olahraga',
            'jumlah' => 25000000,
            'sumber_dana' => 'Pemerintah Kabupaten',
            'keterangan' => 'Bantuan untuk renovasi lapangan voli',
            'user_id' => $adminId
        ]);

        // Pendapatan Lain-lain - Hibah
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-09-10',
            'kategori' => 'Pendapatan Lain-lain',
            'sub_kategori' => 'Hibah dan Sumbangan dari pihak ke3 yang tidak mengikat',
            'deskripsi' => 'Sumbangan dari PT. Maju Jaya',
            'jumlah' => 5000000,
            'sumber_dana' => 'CSR PT. Maju Jaya',
            'keterangan' => 'Sumbangan untuk kegiatan sosial desa',
            'user_id' => $adminId
        ]);

        // Pendapatan Lain-lain - Lelang
        RealisasiPendapatan::create([
            'tahun_anggaran' => 2023,
            'tanggal_realisasi' => '2023-09-15',
            'kategori' => 'Pendapatan Lain-lain',
            'sub_kategori' => 'Lain-lain Pendapatan Desa yang sah',
            'deskripsi' => 'Hasil Lelang Aset Desa',
            'jumlah' => 3000000,
            'sumber_dana' => 'Pemenang Lelang',
            'keterangan' => 'Penjualan motor dinas bekas',
            'user_id' => $adminId
        ]);
    }
} 