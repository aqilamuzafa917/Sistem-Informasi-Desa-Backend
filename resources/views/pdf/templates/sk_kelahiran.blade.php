<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Kelahiran - {{ optional($surat)->nama_bayi ?? 'Bayi' }}</title>
    <link rel="stylesheet" href="{{ public_path('css/surat.css') }}">
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo Kabupaten {{ config('desa.nama_kabupaten') ?? 'Nama Kabupaten' }}" class="logo">
        <div class="header-text">
            <h2>PEMERINTAH KABUPATEN {{ strtoupper(config('desa.nama_kabupaten') ?? 'NAMA KABUPATEN') }}</h2>
            <h3>KECAMATAN {{ strtoupper(config('desa.nama_kecamatan') ?? 'NAMA KECAMATAN') }}</h3>
            <h3>DESA {{ strtoupper(config('desa.nama_desa') ?? 'NAMA DESA') }}</h3>
            <p class="address">{{ config('desa.alamat_desa') ?? 'Alamat Desa ...................................' }}</p>
        </div>
    </div>
    <div class="line"></div>

    <div class="title">SURAT KETERANGAN KELAHIRAN</div>
    <p class="nomor">Nomor: {{ optional($surat)->nomor_surat ?? '..................................................' }}</p>

    <div class="main-content">
        <p>Yang bertanda tangan dibawah ini, {{ config('desa.jabatan_ttd') ?? (config('desa.jabatan_kepala') ?? 'Kepala Desa') }} {{ config('desa.nama_desa') ?? 'Nama Desa' }} Kecamatan {{ config('desa.nama_kecamatan') ?? 'Nama Kecamatan' }} Kabupaten {{ config('desa.nama_kabupaten') ?? 'Nama Kabupaten' }}, menerangkan dengan sebenarnya bahwa:</p>

        <p style="text-align: center; font-weight: bold; margin-top: 15px; margin-bottom: 5px;">Telah Lahir Seorang Anak:</p>
        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td style="width: 35%;">Nama Bayi</td>
                <td style="width: 5%;">:</td>
                <td style="width: 60%;">{{ optional($surat)->nama_bayi ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ optional($surat)->jenis_kelamin_bayi ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Tempat Dilahirkan</td>
                <td>:</td>
                <td>{{ optional($surat)->tempat_dilahirkan ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Tempat Kelahiran</td>
                <td>:</td>
                <td>{{ optional($surat)->tempat_kelahiran ?? '..................................................' }} </td>
            </tr>
            <tr>
                <td>Hari, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ optional($surat)->tanggal_lahir_bayi ? \Carbon\Carbon::parse(optional($surat)->tanggal_lahir_bayi)->locale('id')->isoFormat('dddd, D MMMM YYYY') : '..................................................' }}</td>
            </tr>
            <tr>
                <td>Waktu Lahir</td>
                <td>:</td>
                <td>{{ optional($surat)->waktu_lahir_bayi ? \Carbon\Carbon::parse(optional($surat)->waktu_lahir_bayi)->format('H:i') . ' WIB' : '..................................................' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelahiran</td>
                <td>:</td>
                <td>{{ optional($surat)->jenis_kelahiran ?? '..................................................' }} </td>
            </tr>
            <tr>
                <td>Anak Ke-</td>
                <td>:</td>
                <td>{{ optional($surat)->anak_ke ?? '..................................................' }} </td>
            </tr>
            <tr>
                <td>Penolong Kelahiran</td>
                <td>:</td>
                <td>{{ optional($surat)->penolong_kelahiran ?? '..................................................' }} </td>
            </tr>
            <tr>
                <td>Berat Bayi</td>
                <td>:</td>
                <td>{{ optional($surat)->berat_bayi_kg ? number_format(optional($surat)->berat_bayi_kg, 2, ',', '.') . ' kg' : '......................... kg' }}</td>
            </tr>
            <tr>
                <td>Panjang Bayi</td>
                <td>:</td>
                <td>{{ optional($surat)->panjang_bayi_cm ? number_format(optional($surat)->panjang_bayi_cm, 1, ',', '.') . ' cm' : '......................... cm' }}</td>
            </tr>
        </table>

        <p style="text-align: center; font-weight: bold; margin-top: 15px; margin-bottom: 5px;">Anak dari Pasangan Suami Istri:</p>

        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td style="width: 35%;">Nama Ibu</td>
                <td style="width: 5%;">:</td>
                <td style="width: 60%;">{{ optional($surat)->nama_ibu ?? (optional(optional($surat)->ibuBayi)->nama ?? '..................................................') }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ optional($surat)->nik_penduduk_ibu ?? (optional(optional($surat)->ibuBayi)->nik ?? '..................................................') }}</td>
            </tr>
            <tr>
                <td>Tanggal Lahir / Umur</td>
                <td>:</td>
                <td>{{ optional(optional($surat)->ibuBayi)->tanggal_lahir ? \Carbon\Carbon::parse(optional(optional($surat)->ibuBayi)->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '....................' }} / {{ optional($surat)->umur_ibu_saat_kelahiran ? (int)optional($surat)->umur_ibu_saat_kelahiran . ' Tahun' : '... Tahun' }}</td>
            </tr>
             <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td>{{ optional(optional($surat)->ibuBayi)->pekerjaan ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    {{ optional(optional($surat)->ibuBayi)->alamat ?? '..................................................' }}<br>
                    RT. {{ optional(optional($surat)->ibuBayi)->rt ?? '...' }} RW. {{ optional(optional($surat)->ibuBayi)->rw ?? '...' }} Desa/Kel. {{ optional(optional($surat)->ibuBayi)->desa_kelurahan ?? '....................' }}<br>
                    Kec. {{ optional(optional($surat)->ibuBayi)->kecamatan ?? '....................' }} Kab/Kota. {{ optional(optional($surat)->ibuBayi)->kabupaten_kota ?? '....................' }}
                </td>
            </tr>
        </table>
        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td style="width: 35%;">Nama Ayah</td>
                <td style="width: 5%;">:</td>
                <td style="width: 60%;">{{ optional($surat)->nama_ayah ?? (optional(optional($surat)->ayahBayi)->nama ?? '..................................................') }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ optional($surat)->nik_penduduk_ayah ?? (optional(optional($surat)->ayahBayi)->nik ?? '..................................................') }}</td>
            </tr>
            <tr>
                <td>Tanggal Lahir / Umur</td>
                <td>:</td>
                <td>{{ optional(optional($surat)->ayahBayi)->tanggal_lahir ? \Carbon\Carbon::parse(optional(optional($surat)->ayahBayi)->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '....................' }} / {{ optional($surat)->umur_ayah_saat_kelahiran ? (int)optional($surat)->umur_ayah_saat_kelahiran . ' Tahun' : '... Tahun' }}</td>
            </tr>
             <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td>{{ optional(optional($surat)->ayahBayi)->pekerjaan ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    {{ optional(optional($surat)->ayahBayi)->alamat ?? '..................................................' }}<br>
                    RT. {{ optional(optional($surat)->ayahBayi)->rt ?? '...' }} RW. {{ optional(optional($surat)->ayahBayi)->rw ?? '...' }} Desa/Kel. {{ optional(optional($surat)->ayahBayi)->desa_kelurahan ?? '....................' }}<br>
                    Kec. {{ optional(optional($surat)->ayahBayi)->kecamatan ?? '....................' }} Kab/Kota. {{ optional(optional($surat)->ayahBayi)->kabupaten_kota ?? '....................' }}
                </td>
            </tr>
        </table>

        <p style="font-weight: bold; margin-top: 15px; margin-bottom: 5px;">Pelapor Kelahiran:</p>
        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td style="width: 35%;">Nama</td>
                <td style="width: 5%;">:</td>
                <td style="width: 60%;">{{ optional(optional($surat)->pemohon)->nama ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ optional(optional($surat)->pemohon)->nik ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    {{ optional(optional($surat)->pemohon)->alamat ?? '..................................................' }}
                    <br>RT. {{ optional(optional($surat)->pemohon)->rt ?? '...' }} RW. {{ optional(optional($surat)->pemohon)->rw ?? '...' }} Desa/Kel. {{ optional(optional($surat)->pemohon)->desa_kelurahan ?? '....................' }}
                    <br>Kec. {{ optional(optional($surat)->pemohon)->kecamatan ?? '....................' }} Kab/Kota. {{ optional(optional($surat)->pemohon)->kabupaten_kota ?? '....................' }}
                </td>
            </tr>
        </table>
        <p style="text-align: justify; margin-top: 20px;">Demikian Surat Keterangan Kelahiran ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.</p>
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
                    <span>{{ config('desa.nama_desa') }}, {{ \Carbon\Carbon::parse(optional($surat)->tanggal_disetujui ?? now())->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    <p>PEMOHON,</p>
                    <div class="signature-space"></div>
                    <p class="nama-pemohon">{{ optional($surat->pemohon)->nama ?? '................................' }}</p>
                </td>
            </tr>
        </table>
    </div>
        
</body>
</html>