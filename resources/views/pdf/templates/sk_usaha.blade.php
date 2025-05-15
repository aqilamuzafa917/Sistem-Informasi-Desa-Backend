<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Usaha - {{ optional($surat->pemohon)->nama ?? $surat->nik_pemohon }}</title>
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
    
    <div class="title">SURAT KETERANGAN USAHA</div>
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
                <td>{{ optional($surat->pemohon)->tempat_lahir ?? '....................' }}, {{ optional($surat->pemohon)->tanggal_lahir ? \Carbon\Carbon::parse($surat->pemohon->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '.... ................... 20...' }}</td>
            </tr>
            <tr>
                <td>Kewarganegaraan/Agama</td>
                <td>:</td>
                <td> {{ optional($surat->pemohon)->kewarganegaraan ?? '..................................................' }} / {{ optional($surat->pemohon)->agama ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Status Perkawinan</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->status_perkawinan ?? '..................................................' }}</td>
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
                    RT. {{ optional($surat->pemohon)->rt ?? '00' }} RW. {{ optional($surat->pemohon)->rw ?? '00' }}<br>
                    Desa {{ optional($surat->pemohon)->desa_kelurahan ?? config('desa.nama_desa') ?? '....................' }} Kecamatan {{ optional($surat->pemohon)->kecamatan ?? config('desa.nama_kecamatan') ?? '....................' }}
                </td>
            </tr>
        </table>

        <p style="text-align: justify;">Orang tersebut diatas benar-benar penduduk Desa {{ config('desa.nama_desa') }} Kecamatan {{ config('desa.nama_kecamatan') }} Kabupaten {{ config('desa.nama_kabupaten') }} yang mempunyai usaha dengan keterangan sebagai berikut:</p>

        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td>Nama Usaha</td>
                <td>:</td>
                <td>{{ $surat->nama_usaha ?? '.........................' }}</td>
            </tr>
            <tr>
                <td>Jenis Usaha</td>
                <td>:</td>
                <td>{{ $surat->jenis_usaha ?? '.........................' }}</td>
            </tr>
            <tr>
                <td>Alamat Usaha</td>
                <td>:</td>
                <td>{{ $surat->alamat_usaha ?? '.........................' }}</td>
            </tr>
            <tr>
                <td>Status Bangunan Usaha</td>
                <td>:</td>
                <td>{{ $surat->status_bangunan_usaha ?? '.........................' }}</td>
            </tr>
            <tr>
                <td>Perkiraan Modal Usaha</td>
                <td>:</td>
                <td>Rp. {{ $surat->perkiraan_modal_usaha ? number_format($surat->perkiraan_modal_usaha, 0, ',', '.') : '.........................' }}</td>
            </tr>
            <tr>
                <td>Perkiraan Pendapatan Usaha</td>
                <td>:</td>
                <td>Rp. {{ $surat->perkiraan_pendapatan_usaha ? number_format($surat->perkiraan_pendapatan_usaha, 0, ',', '.') : '.........................' }}</td>
            </tr>
            <tr>
                <td>Jumlah Tenaga Kerja</td>
                <td>:</td>
                <td>{{ $surat->jumlah_tenaga_kerja ?? '.........................' }} Orang</td>
            </tr>
            <tr>
                <td>Usaha Berdiri Sejak</td>
                <td>:</td>
                <td>{{ $surat->sejak_tanggal_usaha ? \Carbon\Carbon::parse($surat->sejak_tanggal_usaha)->locale('id')->isoFormat('D MMMM YYYY') : '.........................' }}</td>
            </tr>
        </table>

        <p style="text-align: justify;">Demikian Surat Keterangan ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.</p>
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