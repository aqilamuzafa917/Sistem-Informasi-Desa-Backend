<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan - {{ $surat->jenis_surat }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
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
            width: 80px;
        }
        .header-text {
            margin: 0 auto;
        }
        .header h2, .header h3 {
            margin: 0;
            font-weight: bold;
        }
        .header h2 {
            font-size: 16px;
        }
        .header h3 {
            font-size: 14px;
        }
        .address {
            font-size: 11px;
            margin-top: 3px;
        }
        .line {
            border-bottom: 2px solid black;
            margin-top: 5px;
            margin-bottom: 25px;
        }
        .content {
            margin-top: 20px;
        }
        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .nomor {
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 12px;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature {
            margin-top: 50px;
            margin-bottom: 10px;
        }
        .main-content {
            margin-top: 20px;
            font-size: 12px;
        }
        .info-table {
            margin-left: 0;
            margin-bottom: 25px;
            font-size: 12px;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        p {
            margin: 8px 0;
            font-size: 12px;
        }
        .secretariat {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-top: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo Kabupaten Bandung Barat" class="logo">
        <div class="header-text">
            <h2>PEMERINTAH KABUPATEN BANDUNG BARAT</h2>
            <h3>KECAMATAN BATUJAJAR</h3>
            <h3>DESA BATUJAJAR TIMUR</h3>
            <p class="address">Jl. Raya Batujajar No.191, Batujajar Tim., Kec. Batujajar, Kabupaten Bandung Barat, Jawa Barat 40561</p>
        </div>
    </div>
    <div class="line"></div>
    
    <div class="title">SURAT KETERANGAN</div>
    <p class="nomor">Nomor: {{ $surat->nomor_surat }}</p>

    <p>Yang bertandatangan dibawah ini, Kepala Desa Batujajar Timur Kecamatan Batujajar Kabupaten Bandung Barat:</p>
    
    <table class="info-table">
        <tr>
            <td width="100">Nomor Surat</td>
            <td width="10">:</td>
            <td>{{ $surat->nomor_surat }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $surat->nama }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $surat->nik }}</td>
        </tr>
        <tr>
            <td>Jenis Surat</td>
            <td>:</td>
            <td>{{ $surat->jenis_surat }}</td>
        </tr>
        <tr>
            <td>Keperluan</td>
            <td>:</td>
            <td>{{ $surat->keperluan }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>{{ $surat->status }}</td>
        </tr>
    </table>

    <p>Demikian surat keterangan ini kami buat dengan sebenarnya, kepada pihak yang berkepentingan agar menjadi tahu serta maklum hendaknya.</p>

    <div class="footer">
        <p>Batujajar Timur, {{ date('d F Y') }}</p>
        <p>Kepala Desa Batujajar Timur</p>
        
        <div class="signature"></div>
    </div>
</body>
</html>