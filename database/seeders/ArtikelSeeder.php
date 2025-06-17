<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArtikelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $artikels = [
            [
                'id_artikel' => 2,
                'jenis_artikel' => 'resmi',
                'status_artikel' => 'disetujui',
                'judul_artikel' => 'Sosialisasi Peledakan (Blasting) PLN di Desa Batujajar Timur',
                'kategori_artikel' => 'Acara',
                'isi_artikel' => 'Pemerintah Desa Batujajar Timur, Kecamatan Batujajar, Kabupaten Bandung Barat, bersama pihak PLN menggelar kegiatan **sosialisasi peledakan (blasting)** yang direncanakan berlangsung di area proyek kelistrikan di wilayah desa. Kegiatan ini bertujuan untuk memberikan pemahaman kepada masyarakat mengenai proses blasting, potensi dampaknya, serta langkah-langkah keselamatan yang akan dilakukan selama kegiatan berlangsung.\r\nAcara yang diselenggarakan di balai desa ini dihadiri oleh berbagai pihak, termasuk unsur perangkat desa, perwakilan dari PLN, TNI/Polri, tokoh masyarakat, serta perwakilan warga sekitar. Kegiatan dimulai dengan sambutan dari Kepala Desa Batujajar Timur dan dilanjutkan dengan pemaparan teknis dari pihak PLN.\r\nAdapun poin-poin penting yang disampaikan dalam sosialisasi ini antara lain:\r\n* Tujuan dan alasan dilakukannya kegiatan peledakan\r\n* Jadwal pelaksanaan serta durasi blasting\r\n* Standar operasional dan prosedur keselamatan yang diterapkan\r\n* Antisipasi terhadap gangguan lingkungan atau kerusakan fasilitas warga\r\n* Mekanisme komunikasi dan pelaporan jika ditemukan dampak negatif\r\nKegiatan ini juga membuka ruang dialog interaktif antara warga dan pihak pelaksana proyek, sehingga masyarakat dapat menyampaikan pertanyaan, kekhawatiran, maupun saran secara langsung.\r\nDengan adanya kegiatan ini, diharapkan masyarakat Desa Batujajar Timur dapat lebih memahami proses proyek yang sedang berjalan, serta merasa lebih tenang dan dilibatkan dalam proses pengambilan keputusan yang berdampak pada lingkungan sekitar.\r\nSosialisasi seperti ini menjadi bentuk nyata dari keterbukaan informasi dan kolaborasi yang sehat antara pemerintah desa, pelaksana proyek, dan masyarakat.',
                'penulis_artikel' => 'Admin Desa',
                'tanggal_kejadian_artikel' => null,
                'tanggal_publikasi_artikel' => '2025-06-17 16:19:03',
                'latitude' => -6.91368864,
                'longitude' => 107.50233943,
                'location_name' => 'Desa Batujajar Timur',
                'created_at' => '2025-06-17 16:19:03',
                'updated_at' => '2025-06-17 16:19:03'
            ],
            [
                'id_artikel' => 3,
                'jenis_artikel' => 'resmi',
                'status_artikel' => 'disetujui',
                'judul_artikel' => 'Desa Batujajar Timur: Pesona Alam yang Menyegarkan Jiwa',
                'kategori_artikel' => 'Budaya',
                'isi_artikel' => 'Desa Batujajar Timur menyuguhkan berbagai destinasi wisata alam yang memesona dan cocok untuk Anda yang ingin melepas penat dari hiruk-pikuk kota. Berikut beberapa tempat wisata alam yang dapat dinikmati di desa ini:\r\n**Curug Bentang**\r\nAir terjun yang satu ini menjadi favorit pengunjung karena keindahan alirannya yang deras dan menyatu harmonis dengan alam sekitar. Terletak di kawasan Sungai Cimeta, Curug Bentang mudah dijangkau dan cocok untuk berwisata bersama keluarga maupun teman.\r\n**Curug Cimeta**\r\nCurug ini menjadi daya tarik karena debit airnya yang deras serta pemandangan hijau di sekelilingnya. Suara gemuruh air yang jatuh menambah kesan alami dan memberikan ketenangan bagi para pengunjung.\r\n**Curug Kapak**\r\nCurug Kapak memiliki cerita unik dari masyarakat setempat yang menamai air terjun ini berdasarkan pohon "kapak" yang banyak tumbuh di sekitarnya. Selain keindahan air terjun, kawasan ini juga menyimpan nilai budaya yang menarik.\r\n**Situ Ciburuy**\r\nDanau yang tenang ini menjadi salah satu spot terbaik untuk menikmati matahari terbenam, memancing, atau hanya sekadar duduk bersantai di tepi air. Suasananya yang damai menjadikan Situ Ciburuy sebagai destinasi relaksasi yang ideal.\r\n**Kebun Fituri**\r\nHamparan kebun hijau yang luas menawarkan pengalaman menyegarkan untuk berjalan-jalan di antara tanaman yang tertata rapi. Cocok untuk keluarga maupun pecinta alam yang ingin menikmati udara segar.\r\n**Lembah Citarum**\r\nMenyusuri Lembah Citarum memberikan pengalaman tak terlupakan dengan pemandangan sungai yang membelah perbukitan hijau. Tempat ini juga menjadi spot favorit bagi penggemar fotografi lanskap.\r\nDengan kombinasi antara keindahan alam, suasana yang tenang, serta keramahan warga lokal, Desa Batujajar Timur menjadi pilihan tepat untuk destinasi wisata akhir pekan atau liburan singkat Anda. Jangan lupa abadikan setiap momen dan rasakan ketenangan yang hanya bisa ditemukan di desa ini.\r\n**Bagikan pesona alam Batujajar Timur dan ajak lebih banyak orang untuk menjelajahi keindahan desa ini!**',
                'penulis_artikel' => 'Admin Desa',
                'tanggal_kejadian_artikel' => null,
                'tanggal_publikasi_artikel' => '2025-06-17 16:17:21',
                'latitude' => -6.91422544,
                'longitude' => 107.5122116,
                'location_name' => 'Desa Batujajar Timur',
                'created_at' => '2025-06-17 16:17:21',
                'updated_at' => '2025-06-17 16:17:21'
            ],
            [
                'id_artikel' => 1,
                'jenis_artikel' => 'resmi',
                'status_artikel' => 'disetujui',
                'judul_artikel' => 'Selamat Datang Di Website Desa Batujajar Timur',
                'kategori_artikel' => 'Budaya',
                'isi_artikel' => 'Sumber Informasi Terbaru Tentang Desa Batujajar Timur',
                'penulis_artikel' => 'Admin Desa',
                'tanggal_kejadian_artikel' => null,
                'tanggal_publikasi_artikel' => '2025-06-17 16:15:22',
                'latitude' => -6.9137994,
                'longitude' => 107.50199605,
                'location_name' => 'Desa Batujajar Timur',
                'created_at' => '2025-06-17 16:15:22',
                'updated_at' => '2025-06-17 16:15:22'
            ]
        ];

        foreach ($artikels as $artikel) {
            DB::table('artikel')->insert($artikel);
        }
    }
} 