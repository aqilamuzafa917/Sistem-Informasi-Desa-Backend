<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Domisili - {{ optional($surat->pemohon)->nama ?? $surat->nik_pemohon }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            font-size: 12px; /* Ukuran font default untuk body */
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            position: relative;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px; /* Sesuaikan ukuran logo jika perlu */
            height: auto;
        }
        .header-text {
            margin: 0 auto; /* Agar teks header terpusat jika logo memakan ruang */
        }
        .header h2, .header h3, .header h4 { /* Menambah h4 untuk nama desa jika diperlukan */
            margin: 0;
            font-weight: bold;
        }
        .header h2 {
            font-size: 16px;
        }
        .header h3 { /* KECAMATAN */
            font-size: 14px;
        }
        .header h4 { /* DESA */
            font-size: 14px; /* Samakan dengan kecamatan atau sesuaikan */
        }
        .address {
            font-size: 10px; /* Alamat kantor desa lebih kecil */
            margin-top: 3px;
        }
        .line {
            border-bottom: 2px solid black;
            margin-top: 5px;
            margin-bottom: 15px; /* Mengurangi margin bawah setelah garis */
        }
        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px; /* Mengurangi margin bawah judul */
        }
        .nomor {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px; /* Margin bawah nomor surat */
            font-size: 12px;
        }
        .main-content p {
            margin: 5px 0; /* Mengurangi margin paragraf */
            text-align: justify;
        }
        .info-table {
            margin-left: 0; /* Tabel rata kiri */
            margin-bottom: 15px; /* Margin bawah tabel */
            font-size: 12px;
            width: 100%; /* Tabel mengisi lebar penuh */
        }
        .info-table td {
            padding: 2px 0; /* Mengurangi padding sel tabel */
            vertical-align: top;
        }
        .info-table td:nth-child(1) { /* Kolom label (Nama, NIK, dll.) */
            width: 180px; /* Lebar tetap untuk label */
        }
        .info-table td:nth-child(2) { /* Kolom titik dua */
            width: 15px;
            text-align: center;
        }
        .signature-section {
            margin-top: 30px;
        }
        .signature-section table {
            width: 100%;
        }
        .signature-section td {
            text-align: center;
            vertical-align: top;
            font-size: 12px;
        }
        .signature-space {
            height: 60px; /* Ruang untuk tanda tangan */
        }
        .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo Kabupaten {{ config('desa.nama_kabupaten') }}" class="logo">
        <div class="header-text">
            <h2>PEMERINTAH KABUPATEN {{ strtoupper(config('desa.nama_kabupaten')) }}</h2>
            <h3>KECAMATAN {{ strtoupper(config('desa.nama_kecamatan')) }}</h3>
            <h3>DESA {{ strtoupper(config('desa.nama_desa')) }}</h3>
            <p class="address">{{ config('desa.alamat_desa') }}</p>
        </div>
    </div>
    <div class="line"></div>
    
    <div class="title">SURAT KETERANGAN</div>
    <p class="nomor">Nomor: {{ $surat->nomor_surat }}</p>

    <div class="main-content">
        <p>Yang bertanda tangan dibawah ini:</p>
        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ config('desa.nama_pejabat_ttd') ?? (config('desa.nama_kepala_desa') ?? '..................................................') }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Jabatan</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    {{ config('desa.jabatan_ttd') ?? (config('desa.jabatan_kepala') ?? 'Kepala Desa') }} {{ config('desa.nama_desa') ?? '....................' }}<br>
                    Kecamatan {{ config('desa.nama_kecamatan') ?? '....................' }} Kabupaten {{ config('desa.nama_kabupaten') ?? '....................' }}<br>
                    Provinsi {{ config('desa.nama_provinsi') ?? '....................' }}
                </td>
            </tr>
        </table>

        <p>Menerangkan dengan sebenarnya bahwa:</p>
        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->nama ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $surat->nik_pemohon ?? (optional($surat->pemohon)->nik ?? '..................................................') }}</td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->tempat_lahir ?? 'Kota' }}, {{ optional($surat->pemohon)->tanggal_lahir ? \Carbon\Carbon::parse($surat->pemohon->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '.... ................... 20...' }}</td>
            </tr>
            <tr>
                <td>Status Perkawinan</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->status_perkawinan ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Agama</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->agama ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->pekerjaan ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    RT. {{ optional($surat->pemohon)->rt ?? '00' }} RW. {{ optional($surat->pemohon)->rw ?? '00' }} <br>
                    Desa {{ optional($surat->pemohon)->desa_kelurahan ?? config('desa.nama_desa') ?? '....................' }} Kecamatan {{ optional($surat->pemohon)->kecamatan ?? config('desa.nama_kecamatan') ?? '....................' }}
                </td>
            </tr>
        </table>

        <p>Orang tersebut diatas benar-benar penduduk Desa {{ config('desa.nama_desa') }} Kecamatan {{ config('desa.nama_kecamatan') }} Kabupaten {{ config('desa.nama_kabupaten') }} Provinsi Jawa Barat. {{-- Asumsi Provinsi Jawa Barat --}}</p>
        <p>Keterangan ini diberikan untuk persyaratan {{ strtolower($surat->keperluan) ?? '..................................................' }}.</p>
        <p>Demikian Surat Keterangan ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="signature-section">
        <table>
            <tr>
                <td style="width: 50%;">
                    <p>&nbsp;</p>
                    <p>{{ strtoupper(config('desa.jabatan_ttd') ?? config('desa.jabatan_kepala')) }} {{ strtoupper(config('desa.nama_desa')) }}</p>
                    <div class="signature-space"></div>
                    <p class="nama-pejabat">{{ config('desa.nama_pejabat_ttd') ?? config('desa.nama_kepala_desa') }}</p>
                    @if(config('desa.nip_pejabat_ttd'))
                    <p>NIP. {{ config('desa.nip_pejabat_ttd') }}</p>
                    @endif
                </td>
                <td style="width: 50%;">
                    
                    <p>{{ config('desa.nama_desa') }}, {{ \Carbon\Carbon::parse(optional($surat)->tanggal_disetujui ?? now())->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    <p>PEMOHON,</p>
                    <div class="signature-space"></div>
                    <p class="nama-pejabat">{{ optional($surat->pemohon)->nama ?? '................................' }}</p>
                </td>
                
            </tr>
        </table>
    </div>
</body>
</html>