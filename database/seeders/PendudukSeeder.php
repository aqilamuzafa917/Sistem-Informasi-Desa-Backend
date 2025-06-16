<?php

namespace Database\Seeders;

use App\Enums\Agama;
use App\Enums\JenisKelamin;
use App\Enums\StatusPerkawinan;
use App\Models\Penduduk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PendudukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Kosongkan tabel untuk menghindari duplikasi saat seeder dijalankan ulang
        DB::statement('SET CONSTRAINTS ALL DEFERRED;');
        Penduduk::truncate();
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE;');

        $faker = Faker::create('id_ID');

        // Data statis untuk beberapa kota di Indonesia
        $kotaLahir = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Yogyakarta', 'Denpasar', 'Banjarmasin'];
        
        // Data statis untuk pekerjaan umum
        $pekerjaan = [
            'Belum / Tidak Bekerja',
            'Mengurus Rumah Tangga',
            'Pelajar / Mahasiswa',
            'Pensiunan',
            'Pegawai Negeri Sipil (PNS)',
            'Tentara Nasional Indonesia (TNI)',
            'Kepolisian RI',
            'Perdagangan',
            'Petani / Pekebun',
            'Peternak',
            'Nelayan / Perikanan',
            'Industri',
            'Konstruksi',
            'Transportasi',
            'Karyawan Swasta',
            'Karyawan BUMN',
            'Karyawan BUMD',
            'Karyawan Honorer',
            'Buruh Harian Lepas',
            'Buruh Tani / Perkebunan',
            'Buruh Nelayan / Perikanan',
            'Buruh Peternakan',
            'Pembantu Rumah Tangga',
            'Tukang Cukur',
            'Tukang Listrik',
            'Tukang Batu',
            'Tukang Kayu',
            'Tukang Sol Sepatu',
            'Tukang Las / Pandai Besi',
            'Tukang Jahit',
            'Tukang Gigi',
            'Penata Rias',
            'Penata Busana',
            'Penata Rambut',
            'Mekanik',
            'Seniman',
            'Artis',
            'Dokter',
            'Bidan',
            'Perawat',
            'Apoteker',
            'Psikiater / Psikolog',
            'Penyiar Televisi',
            'Penyiar Radio',
            'Pelaut',
            'Peneliti',
            'Sopir',
            'Pialang',
            'Paranormal',
            'Pedagang',
            'Perangkat Desa',
            'Kepala Desa',
            'Biarawan / Biarawati',
            'Wirausaha',
            'Wiraswasta',
            'Konsultan',
            'Notaris',
            'Arsitek',
            'Akuntan',
            'Juru Masak',
            'Wartawan',
            'Ustadz / Mubaligh',
            'Juru Sembelih',
            'Imam Masjid',
            'Pendeta',
            'Pastor',
            'Aktivis LSM',
            'Anggota DPR-RI',
            'Anggota DPD',
            'Anggota BPK',
            'Presiden',
            'Wakil Presiden',
            'Anggota Mahkamah Konstitusi',
            'Anggota Kabinet / Menteri',
            'Duta Besar',
            'Gubernur',
            'Wakil Gubernur',
            'Bupati',
            'Wakil Bupati',
            'Walikota',
            'Wakil Walikota',
            'Anggota DPRD Provinsi',
            'Anggota DPRD Kabupaten/Kota',
            'Dosen',
            'Guru',
            'Pilot',
            'Pengacara',
            'Hakim',
            'Jaksa',
            'Manajer',
            'Programmer',
            'Penata Musik',
            'Penata Tari',
            'Perancang Busana',
            'Penyelam',
            'Penyelidik',
            'Pembimbing Kemasyarakatan',
            'Pramugari / Pramugara',
            'Teknisi',
            'Tenaga Pengajar',
            'Lainnya'
        ];
        
        // Array untuk menampung semua data penduduk yang akan di-insert
        $pendudukData = [];
        
        // Jumlah keluarga yang ingin dibuat
        $jumlahKeluarga = 250; 

        for ($i = 0; $i < $jumlahKeluarga; $i++) {
            // --- Generate Data Keluarga ---
            $noKK = $faker->unique()->numerify('3271##########');
            $alamat = $faker->streetAddress;
            $rt = $faker->numerify('00#');
            $rw = $faker->numerify('00#');
            $desa = 'Desa Sukamaju';
            $kecamatan = 'Kecamatan Maju Jaya';
            $kabupaten = 'Kabupaten Sejahtera';
            $provinsi = 'Jawa Barat';
            $kodePos = $faker->numerify('16###');
            // Agama cenderung sama dalam satu keluarga
            $agamaKeluarga = $faker->randomElement(Agama::cases());

            // --- Kepala Keluarga (Umumnya Laki-laki) ---
            $kepalaKeluargaTglLahir = Carbon::parse($faker->dateTimeBetween('-60 years', '-25 years'));
            $umurKepalaKeluarga = $kepalaKeluargaTglLahir->age;

            $pendudukData[] = $this->generatePendudukData(
                $faker,
                [
                    'nik' => $faker->unique()->numerify('3271##########'),
                    'nama' => $faker->name('male'),
                    'jenis_kelamin' => JenisKelamin::LakiLaki,
                    'tanggal_lahir' => $kepalaKeluargaTglLahir->toDateString(),
                    'status_perkawinan' => $faker->randomElement([StatusPerkawinan::Menikah, StatusPerkawinan::CeraiHidup, StatusPerkawinan::CeraiMati]),
                    'pekerjaan' => $faker->randomElement($pekerjaan),
                    'pendidikan' => $this->getPendidikanByAge($umurKepalaKeluarga),
                ],
                $noKK, $alamat, $rt, $rw, $desa, $kecamatan, $kabupaten, $provinsi, $kodePos, $agamaKeluarga, $kotaLahir
            );

            // --- Istri (Jika status kepala keluarga Menikah) ---
            $statusPerkawinanKepalaKeluarga = end($pendudukData)['status_perkawinan'];
            if ($statusPerkawinanKepalaKeluarga === StatusPerkawinan::Menikah->value) {
                // Buat tanggal lahir istri berdekatan dengan suami
                $istriTglLahir = Carbon::parse($faker->dateTimeBetween($kepalaKeluargaTglLahir->copy()->subYears(5), $kepalaKeluargaTglLahir->copy()->addYears(2)));
                $umurIstri = $istriTglLahir->age;

                $pendudukData[] = $this->generatePendudukData(
                    $faker,
                    [
                        'nik' => $faker->unique()->numerify('3271##########'),
                        'nama' => $faker->name('female'),
                        'jenis_kelamin' => JenisKelamin::Perempuan,
                        'tanggal_lahir' => $istriTglLahir->toDateString(),
                        'status_perkawinan' => StatusPerkawinan::Menikah,
                        'pekerjaan' => $faker->randomElement(['Mengurus Rumah Tangga', 'Karyawan Swasta', 'Guru', 'Bidan']),
                        'pendidikan' => $this->getPendidikanByAge($umurIstri),
                    ],
                    $noKK, $alamat, $rt, $rw, $desa, $kecamatan, $kabupaten, $provinsi, $kodePos, $agamaKeluarga, $kotaLahir
                );
            }
            
            // --- Anak-anak (0 sampai 4 anak) ---
            $jumlahAnak = rand(0, 4);
            for ($j = 0; $j < $jumlahAnak; $j++) {
                // Usia anak lebih muda dari orang tua
                $anakTglLahir = Carbon::parse($faker->dateTimeBetween($kepalaKeluargaTglLahir->copy()->addYears(18), '-1 years'));
                $umurAnak = $anakTglLahir->age;

                $pendudukData[] = $this->generatePendudukData(
                    $faker,
                    [
                        'nik' => $faker->unique()->numerify('3271##########'),
                        'nama' => $faker->name(),
                        'jenis_kelamin' => $faker->randomElement(JenisKelamin::cases()),
                        'tanggal_lahir' => $anakTglLahir->toDateString(),
                        'status_perkawinan' => StatusPerkawinan::BelumMenikah,
                        'pekerjaan' => $this->getPekerjaanByAge($umurAnak, $pekerjaan),
                        'pendidikan' => $this->getPendidikanByAge($umurAnak),
                    ],
                    $noKK, $alamat, $rt, $rw, $desa, $kecamatan, $kabupaten, $provinsi, $kodePos, $agamaKeluarga, $kotaLahir
                );
            }
        }
        
        // Insert data ke database dalam chunk untuk performa yang lebih baik
        foreach (array_chunk($pendudukData, 500) as $chunk) {
            Penduduk::insert($chunk);
        }
    }

    /**
     * Helper function untuk membuat array data satu penduduk.
     */
    private function generatePendudukData(
        $faker, array $specifics,
        string $noKK, string $alamat, string $rt, string $rw, string $desa, string $kecamatan,
        string $kabupaten, string $provinsi, string $kodePos, Agama $agama, array $kotaLahir
    ): array
    {
        $now = Carbon::now();
        return array_merge([
            'tempat_lahir' => $faker->randomElement($kotaLahir),
            'alamat' => $alamat,
            'rt' => $rt,
            'rw' => $rw,
            'desa_kelurahan' => $desa,
            'kecamatan' => $kecamatan,
            'kabupaten_kota' => $kabupaten,
            'provinsi' => $provinsi,
            'kode_pos' => $kodePos,
            'agama' => $agama,
            'kewarganegaraan' => 'WNI',
            'no_kk' => $noKK,
            'created_at' => $now,
            'updated_at' => $now,
        ], $specifics);
    }

    /**
     * Helper function untuk menentukan pendidikan berdasarkan usia.
     */
    private function getPendidikanByAge(int $age): string
    {
        if ($age <= 6) return 'Tidak/Belum Sekolah';
        if ($age <= 12) return 'SD/Sederajat';
        if ($age <= 15) return 'SLTP/Sederajat';
        if ($age <= 18) return 'SLTA/Sederajat';
        if ($age <= 23) return $this->randomElement(['SLTA/Sederajat', 'Diploma I/II', 'Diploma III', 'S1']);
        return $this->randomElement(['SLTA/Sederajat', 'S1', 'S2', 'S3']);
    }

    /**
     * Helper function untuk menentukan pekerjaan berdasarkan usia.
     */
    private function getPekerjaanByAge(int $age, array $pekerjaan): string
    {
        if ($age <= 15) return 'Pelajar / Mahasiswa';
        if ($age <= 22) return $this->randomElement(['Pelajar / Mahasiswa', 'Belum / Tidak Bekerja', 'Karyawan Swasta']);
        if ($age >= 60) return $this->randomElement(['Pensiunan', 'Wiraswasta', 'Belum / Tidak Bekerja']);
        return $this->randomElement(array_diff($pekerjaan, ['Pelajar / Mahasiswa', 'Pensiunan']));
    }

    /**
     * Custom random_element to avoid errors with empty arrays if needed.
     */
    private function randomElement(array $array)
    {
        if (empty($array)) {
            return null;
        }
        return $array[array_rand($array)];
    }
}