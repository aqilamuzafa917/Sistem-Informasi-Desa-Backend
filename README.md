<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# Sistem Informasi Desa - API Surat

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

API untuk pengelolaan dan pengajuan berbagai jenis surat administrasi desa. Sistem ini memungkinkan warga untuk mengajukan surat secara online dan administrator desa untuk mengelola pengajuan tersebut.

## Daftar Isi
- [Fitur Utama](#fitur-utama)
- [Endpoint API](#endpoint-api)
- [Panduan Penggunaan](#panduan-penggunaan)
  - [Membuat Pengajuan Surat](#1-membuat-pengajuan-surat-baru)
  - [Contoh Data Pengajuan](#contoh-data-pengajuan-berdasarkan-jenis-surat)
  - [Melihat Status Pengajuan](#6-melihat-daftar-surat-berdasarkan-nik-pemohon-publik)
  - [Mengunduh Surat](#7-mengunduh-pdf-surat-publik)
- [Operasi Admin](#operasi-admin)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

## Fitur Utama

- Pengajuan berbagai jenis surat (Kematian, Pindah, Kelahiran, Usaha, dll)
- Pelacakan status pengajuan berdasarkan NIK
- Manajemen pengajuan surat oleh admin desa
- Pembuatan dokumen PDF otomatis untuk surat yang disetujui
- Validasi data sesuai jenis surat yang diajukan

## Endpoint API

| Method | Endpoint                   | Fungsi                               | Autentikasi |
|--------|----------------------------|--------------------------------------|-------------|
| POST   | `/api/surat`               | Membuat pengajuan surat baru         | Tidak       |
| GET    | `/api/surat`               | Menampilkan daftar surat (Admin)     | Admin       |
| GET    | `/api/surat/{id_surat}`    | Melihat detail surat (Admin)         | Admin       |
| PUT    | `/api/surat/{id_surat}/status` | Memperbarui status surat         | Admin       |
| DELETE | `/api/surat/{id_surat}`    | Menghapus pengajuan surat            | Admin       |
| GET    | `/api/surat/nik/{nik}`     | Melihat daftar surat berdasarkan NIK | Tidak       |
| GET    | `/api/surat/pdf/{id_surat}`| Mengunduh PDF surat                  | Tidak       |

## Panduan Penggunaan

### 1. Membuat Pengajuan Surat Baru

**Endpoint:** `POST /api/surat`

**Field umum yang wajib diisi:**
- `nik_pemohon` - NIK 16 digit yang terdaftar di database penduduk
- `jenis_surat` - Jenis surat yang diajukan (lihat daftar di bawah)
- `keperluan` - Tujuan pengajuan surat (maksimal 500 karakter)
- `tanggal_request` - Tanggal pengajuan (opsional, format: YYYY-MM-DD)
- `attachment_bukti_pendukung` - File pendukung opsional (jpg/jpeg/png/pdf, maks: 2MB)

### Contoh Data Pengajuan Berdasarkan Jenis Surat

#### SK_KEMATIAN
```json
{
    "nik_pemohon": "3201xxxxxxxxxxxx",
    "jenis_surat": "SK_KEMATIAN",
    "keperluan": "Mengurus akta kematian dan klaim asuransi",
    "nik_penduduk_meninggal": "3201yyyyyyyyyyyy",
    "tanggal_kematian": "2024-03-10",
    "waktu_kematian": "15:30",
    "tempat_kematian": "Rumah Sakit ABC",
    "penyebab_kematian": "Sakit Jantung",
    "hubungan_pelapor_kematian": "Anak Kandung"
}
```

#### SK_PINDAH
```json
{
    "nik_pemohon": "3201xxxxxxxxxxxx",
    "jenis_surat": "SK_PINDAH",
    "keperluan": "Pindah domisili ke luar kota",
    "alamat_tujuan": "Jl. Merdeka No. 10",
    "rt_tujuan": "005",
    "rw_tujuan": "002",
    "kelurahan_desa_tujuan": "Sukmajaya",
    "kecamatan_tujuan": "Cimanggis",
    "kabupaten_kota_tujuan": "Kota Depok",
    "provinsi_tujuan": "Jawa Barat",
    "alasan_pindah": "Mengikuti suami",
    "klasifikasi_pindah": "Antar Kabupaten/Kota",
    "data_pengikut_pindah": [
        {"nik": "3201aaaaaaaaaaaa", "nama": "Nama Anak 1", "hubungan": "Anak"},
        {"nik": "3201bbbbbbbbbbbb", "nama": "Nama Anak 2", "hubungan": "Anak"}
    ]
}
```

#### SK_KELAHIRAN
```json
{
    "nik_pemohon": "3201ibuibuibubbb",
    "jenis_surat": "SK_KELAHIRAN",
    "keperluan": "Pembuatan Akta Kelahiran",
    "nama_bayi": "Budi Santoso",
    "tempat_dilahirkan": "Rumah Bersalin Sehat",
    "tempat_kelahiran": "Bogor",
    "tanggal_lahir_bayi": "2024-03-10",
    "waktu_lahir_bayi": "08:15",
    "jenis_kelamin_bayi": "Laki-laki",
    "jenis_kelahiran": "Tunggal",
    "anak_ke": 1,
    "penolong_kelahiran": "Bidan",
    "berat_bayi_kg": 3.1,
    "panjang_bayi_cm": 50.5,
    "nik_penduduk_ibu": "3201ibuibuibubbb",
    "nik_penduduk_ayah": "3201ayahayahayah",
    "nik_penduduk_pelapor_lahir": "3201ibuibuibubbb",
    "hubungan_pelapor_lahir": "Ibu Kandung"
}
```

#### SK_USAHA
```json
{
    "nik_pemohon": "3201usahawanxxxx",
    "jenis_surat": "SK_USAHA",
    "keperluan": "Pengajuan pinjaman KUR",
    "nama_usaha": "Warung Makan Sedap Mantap",
    "jenis_usaha": "Kuliner",
    "alamat_usaha": "Jl. Raya Desa No. 45",
    "status_bangunan_usaha": "Sewa",
    "perkiraan_modal_usaha": 15000000,
    "perkiraan_pendapatan_usaha": 5000000,
    "jumlah_tenaga_kerja": 2,
    "sejak_tanggal_usaha": "2022-01-15"
}
```

#### REKOM_KIP
```json
{
    "nik_pemohon": "3201ortuxxxxxxxx",
    "jenis_surat": "REKOM_KIP",
    "keperluan": "Pengajuan Kartu Indonesia Pintar",
    "penghasilan_perbulan_kepala_keluarga": 1500000,
    "pekerjaan_kepala_keluarga": "Buruh Harian Lepas",
    "nik_penduduk_siswa": "3201siswasiswaaa",
    "nama_sekolah": "SDN Desa Makmur 01",
    "nisn_siswa": "0012345678",
    "kelas_siswa": "5"
}
```

#### SKTM atau REKOM_KIS
```json
{
    "nik_pemohon": "3201kkkkkkkkkkkk",
    "jenis_surat": "SKTM",
    "keperluan": "Pengajuan Bantuan Sosial / KIS",
    "penghasilan_perbulan_kepala_keluarga": 800000,
    "pekerjaan_kepala_keluarga": "Petani"
}
```

#### SK_KEHILANGAN_KTP
```json
{
    "nik_pemohon": "3201hilangktpxxx",
    "jenis_surat": "SK_KEHILANGAN_KTP",
    "keperluan": "Mengurus penerbitan KTP baru",
    "nomor_ktp_hilang": "3201hilangktpxxx",
    "tanggal_perkiraan_hilang": "2024-03-08",
    "lokasi_perkiraan_hilang": "Pasar Desa",
    "kronologi_singkat": "KTP diperkirakan terjatuh saat berbelanja di pasar sekitar pukul 10 pagi.",
    "nomor_laporan_polisi": "LP/B/123/III/2024/SPKT/POLSEK",
    "tanggal_laporan_polisi": "2024-03-09"
}
```

### 6. Melihat Daftar Surat Berdasarkan NIK Pemohon (Publik)

**Endpoint:** `GET /api/surat/nik/{nik}`

Warga dapat memeriksa status pengajuan surat mereka dengan menyediakan NIK.

**Contoh URL:** `/api/surat/nik/3201xxxxxxxxxxxx`

### 7. Mengunduh PDF Surat (Publik)

**Endpoint:** `GET /api/surat/pdf/{id_surat}`

Setelah surat disetujui, warga dapat mengunduh surat dalam format PDF.

**Contoh URL:** `/api/surat/pdf/15`

## Operasi Admin

### 2. Melihat Daftar Surat (Admin)

**Endpoint:** `GET /api/surat`

Administrator dapat melihat semua pengajuan surat dengan dukungan pagination dan filtering.

**Query Parameters:**
- `status` - Filter berdasarkan status (`Pending`, `Approved`, `Rejected`)
- `page` - Nomor halaman
- `per_page` - Jumlah item per halaman

### 3. Melihat Detail Surat (Admin)

**Endpoint:** `GET /api/surat/{id_surat}`

Administrator dapat melihat detail lengkap pengajuan surat.

### 4. Memperbarui Status Surat (Admin)

**Endpoint:** `PUT /api/surat/{id_surat}/status`

Administrator dapat menyetujui atau menolak pengajuan surat dengan menambahkan catatan.

**Contoh Request Body (JSON):**
```json
{
    "status_surat": "Approved",
    "catatan": "Data lengkap, silahkan ambil surat di kantor desa."
}
```

## Persyaratan Sistem

- PHP 8.0 atau lebih tinggi
- Laravel 12
- Database MySQL/PostgreSQL/SQLite
- Composer

## Instalasi

1. Clone repositori:
   ```bash
   git clone https://github.com/aqilamuzafa917/Sistem-Informasi-Desa-Backend.git
   cd sistem-informasi-desa-api-surat
   ```

2. Instal dependensi:
   ```bash
   composer install
   ```

3. Salin file konfigurasi:
   ```bash
   cp .env.example .env
   ```

4. Konfigurasikan database di file `.env`

5. Jalankan migrasi database:
   ```bash
   php artisan migrate
   ```

6. Jalankan server:
   ```bash
   php artisan serve
   ```

## Catatan Penting

1. **Autentikasi**: Endpoint admin memerlukan token autentikasi (Bearer Token) di header `Authorization`.

2. **NIK**: Semua NIK yang digunakan dalam request harus sudah terdaftar di tabel `penduduks`.

3. **Format Data**:
   - Format tanggal: `YYYY-MM-DD`
   - Format waktu: `HH:MM` atau `HH:MM:SS`

4. **Unggah File**: Untuk request yang menyertakan file, gunakan `Content-Type: multipart/form-data`.

5. **Nomor Surat**: Nomor surat akan digenerate otomatis ketika status diubah menjadi `Approved`.

## Kontribusi

Kontribusi sangat diterima! Silakan buka Issue atau Pull Request.

1. Fork repositori
2. Buat branch fitur (`git checkout -b feature/amazing-feature`)
3. Commit perubahan (`git commit -m 'Add some amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buka Pull Request

## Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT - lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.
