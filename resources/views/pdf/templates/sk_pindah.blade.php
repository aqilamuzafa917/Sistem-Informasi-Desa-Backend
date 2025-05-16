<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Pindah - {{ optional($surat->pemohon)->nama ?? $surat->nik_pemohon }}</title>
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
    
    <div class="title">SURAT KETERANGAN PINDAH</div>
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
                <td>KK</td>
                <td>:</td>
                <td>{{ $surat->kk_pemohon ?? (optional($surat->pemohon)->kk ?? '..................................................') }}</td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->tempat_lahir ?? '....................' }}, {{ optional($surat->pemohon)->tanggal_lahir ? \Carbon\Carbon::parse($surat->pemohon->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '.... ................... 20...' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->jenis_kelamin ?? '..................................................' }}</td>
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
                <td style="vertical-align: top;">Alamat Asal</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    RT. {{ optional($surat->pemohon)->rt ?? '00' }} RW. {{ optional($surat->pemohon)->rw ?? '00' }}<br>
                    Desa {{ optional($surat->pemohon)->desa_kelurahan ?? config('desa.nama_desa') ?? '....................' }} Kecamatan {{ optional($surat->pemohon)->kecamatan ?? config('desa.nama_kecamatan') ?? '....................' }}
                </td>
            </tr>
        </table>

        <p>Orang tersebut diatas benar-benar penduduk Desa {{ config('desa.nama_desa') }} Kecamatan {{ config('desa.nama_kecamatan') }} Kabupaten {{ config('desa.nama_kabupaten') }} Provinsi {{ config('desa.nama_provinsi') }} yang akan pindah ke:</p>

        <table class="info-table" style="margin-left: 20px;">
            <tr>
                <td style="vertical-align: top;">Alamat Tujuan</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    {{ $surat->alamat_tujuan ?? '..................................................' }}<br>
                    RT. {{ $surat->rt_tujuan ?? '00' }} RW. {{ $surat->rw_tujuan ?? '00' }}<br>
                    Desa/Kelurahan {{ $surat->kelurahan_desa_tujuan ?? '....................' }}<br>
                    Kecamatan {{ $surat->kecamatan_tujuan ?? '....................' }}<br>
                    Kabupaten/Kota {{ $surat->kabupaten_kota_tujuan ?? '....................' }}<br>
                    Provinsi {{ $surat->provinsi_tujuan ?? '....................' }}
                </td>
            </tr>
            <tr>
                <td>Alasan Pindah</td>
                <td>:</td>
                <td>{{ $surat->alasan_pindah ?? '..................................................' }}</td>
            </tr>
            <tr>
                <td>Klasifikasi Pindah</td>
                <td>:</td>
                <td>{{ $surat->klasifikasi_pindah ?? '..................................................' }}</td>
            </tr>
        </table>

        @if(isset($surat->data_pengikut_pindah) && is_array($surat->data_pengikut_pindah) && count($surat->data_pengikut_pindah) > 0)
        <p>Pengikut/Anggota Keluarga yang ikut pindah: <strong>{{ count($surat->data_pengikut_pindah) }} orang</strong></p>
        <table class="data-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 5px;">No</th>
                    <th style="border: 1px solid #000; padding: 5px;">NIK</th>
                    <th style="border: 1px solid #000; padding: 5px;">Nama</th>
                    <th style="border: 1px solid #000; padding: 5px;">Tempat, Tgl Lahir</th>
                    <th style="border: 1px solid #000; padding: 5px;">Jenis Kelamin</th>
                    <th style="border: 1px solid #000; padding: 5px;">Status Perkawinan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($surat->data_pengikut_pindah as $index => $pengikut)
                <tr>
                    <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 5px;">{{ $pengikut['nik'] ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 5px;">{{ $pengikut['nama'] ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 5px;">{{ $pengikut['tempat_lahir'] ?? '-' }}, {{ isset($pengikut['tanggal_lahir']) ? \Carbon\Carbon::parse($pengikut['tanggal_lahir'])->locale('id')->isoFormat('D MMMM YYYY') : '-' }}</td>
                    <td style="border: 1px solid #000; padding: 5px;">{{ $pengikut['jenis_kelamin'] ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 5px;">{{ $pengikut['status_perkawinan']?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <p style="text-align: justify; margin-top: 20px;">Demikian Surat Keterangan Pindah ini dibuat dengan sebenarnya dan untuk dipergunakan sebagaimana mestinya.</p>
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