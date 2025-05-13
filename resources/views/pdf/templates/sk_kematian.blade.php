<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kematian - {{ $surat->nama_meninggal ?? $surat->nik_meninggal }}</title>
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
        .header h2, .header h3, .header h4 {
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
            font-size: 14px;
        }
        .address {
            font-size: 10px; /* Alamat kantor desa lebih kecil */
            margin-top: 3px;
        }
        .line {
            border-bottom: 2px solid black;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .nomor {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .main-content p {
            margin: 5px 0;
            text-align: justify;
        }
        .info-table {
            margin-left: 0;
            margin-bottom: 15px;
            font-size: 12px;
            width: 100%;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-table td:nth-child(1) { /* Kolom label */
            width: 180px;
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
    
    <div class="title">SURAT KETERANGAN KEMATIAN</div>
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
                    Kecamatan {{ config('desa.nama_kecamatan') ?? '....................' }} Kabupaten {{ config('desa.nama_kabupaten') ?? '....................' }}
                </td>
            </tr>
        </table>

        <p>Menerangkan dengan sebenarnya bahwa:</p>
        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><b>{{ $surat->nama_meninggal ?? '..................................................' }}</b></td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $surat->nik_meninggal ?? '...................................................' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $surat->jenis_kelamin_meninggal ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $surat->tempat_lahir_meninggal ?? 'Kota' }}, {{ $surat->tanggal_lahir_meninggal ? \Carbon\Carbon::parse($surat->tanggal_lahir_meninggal)->locale('id')->isoFormat('D MMMM YYYY') : '.... ................... 20...' }}</td>
            </tr>
            <tr>
                <td>Umur</td>
                <td>:</td>
                <td>{{ $surat->umur_saat_meninggal ? $surat->umur_saat_meninggal . ' Tahun' : '.... Tahun' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    RT. {{ optional($surat->pendudukMeninggal)->rt ?? '00' }} RW. {{ optional($surat->pendudukMeninggal)->rw ?? '00' }} <br>
                    Desa {{ optional($surat->pendudukMeninggal)->desa_kelurahan ?? config('desa.nama_desa') ?? '....................' }} Kecamatan {{ optional($surat->pendudukMeninggal)->kecamatan ?? config('desa.nama_kecamatan') ?? '....................' }}
                </td>
            </tr>
        </table>

        <p>Telah meninggal dunia pada:</p>
        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td>Hari</td>
                <td>:</td>
                <td>{{ $surat->hari_kematian ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ $surat->tanggal_kematian ? \Carbon\Carbon::parse($surat->tanggal_kematian)->locale('id')->isoFormat('D MMMM YYYY') : '.... ................... 20...' }}</td>
            </tr>
            <tr>
                <td>Di</td>
                <td>:</td>
                <td>{{ $surat->tempat_kematian ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Disebabkan</td>
                <td>:</td>
                <td>{{ $surat->penyebab_kematian ?? '..................................................' }}</td>
            </tr>
        </table>

        <p>Demikian Surat Keterangan ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="signature-section">
        <table>
            <tr>
                <td style="width: 100%; text-align: right;">
                    <p>{{ config('desa.nama_desa') }}, {{ \Carbon\Carbon::parse(optional($surat)->tanggal_disetujui ?? now())->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    <p>{{ strtoupper(config('desa.jabatan_ttd') ?? config('desa.jabatan_kepala')) }} {{ strtoupper(config('desa.nama_desa')) }}</p>
                    <div class="signature-space"></div>
                    <p class="nama-pejabat">{{ config('desa.nama_pejabat_ttd') ?? config('desa.nama_kepala_desa') }}</p>
                    @if(config('desa.nip_pejabat_ttd'))
                    <p>NIP. {{ config('desa.nip_pejabat_ttd') }}</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>