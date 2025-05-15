<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kematian - {{ $surat->nama_meninggal ?? $surat->nik_meninggal }}</title>
    <link rel="stylesheet" href="{{ public_path('css/surat.css') }}">
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