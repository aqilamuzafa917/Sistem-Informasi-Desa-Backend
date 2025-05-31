<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan APB Desa - Tahun {{ $tahun }}</title>
    <link rel="stylesheet" href="{{ public_path('css/surat.css') }}">
    <style>
        body {
            font-size: 10pt;
            line-height: 1.4;
        }
        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .table-bordered th {
            background-color: #d0d0d0;
            font-weight: bold;
            color: #333;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .bg-gray {
            background-color: #e0e0e0;
        }
        .indent-1 {
            padding-left: 25px;
        }
        .indent-2 {
            padding-left: 50px;
        }
        .indent-3 {
            padding-left: 75px;
        }
        .page-break {
            page-break-after: always;
        }
        /* Remove alternating row colors to avoid conflicts */
        .table-bordered tbody tr {
            background-color: white;
        }
        /* Override for specific gray background rows */
        .table-bordered tbody tr.bg-gray {
            background-color: #e0e0e0 !important;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
            float: left;
            margin-right: 20px;
        }
        .header-text {
            text-align: center;
        }
        .header-text h2, .header-text h3 {
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .header-text h2 {
            font-size: 14pt;
        }
        .header-text h3 {
            font-size: 12pt;
        }
        .address {
            font-size: 9pt;
            margin-top: 5px;
        }
        .line {
            border-top: 2px solid #000;
            margin-bottom: 15px;
            clear: both;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .signature-section table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-section td {
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 70px;
        }
        .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo Kabupaten {{ config('desa.nama_kabupaten') ?? 'Nama Kabupaten' }}" class="logo">
        <div class="header-text">
            <h2>PEMERINTAH KABUPATEN {{ strtoupper(config('desa.nama_kabupaten') ?? 'NAMA KABUPATEN') }}</h2>
            <h3>KECAMATAN {{ strtoupper(config('desa.nama_kecamatan') ?? 'NAMA KECAMATAN') }}</h3>
            <h3>DESA {{ strtoupper(config('desa.nama_desa')) ?? ('NAMA DESA') }}</h3>
            <p class="address">{{ config('desa.alamat_desa') ?? 'Alamat Desa ...................................' }}</p>
        </div>
    </div>
    <div class="line"></div>

    <div class="title">ANGGARAN PENDAPATAN DAN BELANJA DESA</div>
    <p class="text-center">PEMERINTAH DESA {{ strtoupper(config('desa.nama_desa')) }}</p>
    <p class="text-center">TAHUN ANGGARAN {{ $tahun }}</p>

    <div class="main-content">
        <table class="table-bordered">
            <thead>
                <tr>
                    <th class="text-center" style="width: 15%;">KODE REK</th>
                    <th class="text-center" style="width: 55%;">URAIAN</th>
                    <th class="text-center" style="width: 20%;">ANGGARAN (Rp)</th>
                    <th class="text-center" style="width: 10%;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <!-- PENDAPATAN -->
                <tr>
                    <td class="font-bold">1.</td>
                    <td class="font-bold">PENDAPATAN</td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- 1.1. Pendapatan Asli Desa -->
                <tr>
                    <td>1.1.</td>
                    <td class="font-bold">Pendapatan Asli Desa</td>
                    <td class="text-right font-bold">{{ number_format($total_pendapatan_asli_desa, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
                @foreach($pendapatan_asli_desa as $index => $item)
                <tr>
                    <td>1.1.{{ $index + 1 }}</td>
                    <td class="indent-1">{{ $item['uraian'] }}</td>
                    <td class="text-right">{{ number_format($item['jumlah'], 2, ',', '.') }}</td>
                    <td>{{ $item['keterangan'] ?? '' }}</td>
                </tr>
                @endforeach

                <!-- 1.2. Pendapatan Transfer -->
                <tr>
                    <td>1.2.</td>
                    <td class="font-bold">Pendapatan Transfer</td>
                    <td class="text-right font-bold">{{ number_format($total_pendapatan_transfer, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
                @foreach($pendapatan_transfer as $index => $item)
                <tr>
                    <td>1.2.{{ $index + 1 }}</td>
                    <td class="indent-1">{{ $item['uraian'] }}</td>
                    <td class="text-right">{{ number_format($item['jumlah'], 2, ',', '.') }}</td>
                    <td>{{ $item['keterangan'] ?? '' }}</td>
                </tr>
                @endforeach

                <!-- 1.3. Pendapatan Lain-Lain -->
                <tr>
                    <td>1.3.</td>
                    <td class="font-bold">Pendapatan Lain-Lain</td>
                    <td class="text-right font-bold">{{ number_format($total_pendapatan_lain, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
                @foreach($pendapatan_lain as $index => $item)
                <tr>
                    <td>1.3.{{ $index + 1 }}</td>
                    <td class="indent-1">{{ $item['uraian'] }}</td>
                    <td class="text-right">{{ number_format($item['jumlah'], 2, ',', '.') }}</td>
                    <td>{{ $item['keterangan'] ?? '' }}</td>
                </tr>
                @endforeach

                <!-- JUMLAH PENDAPATAN -->
                <tr class="bg-gray">
                    <td></td>
                    <td class="font-bold">JUMLAH PENDAPATAN</td>
                    <td class="text-right font-bold">{{ number_format($total_pendapatan, 2, ',', '.') }}</td>
                    <td></td>
                </tr>

                <!-- BELANJA -->
                <tr>
                    <td class="font-bold">2.</td>
                    <td class="font-bold">BELANJA</td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Bidang-bidang Belanja -->
                @php
                    $displayed_categories = [];
                @endphp

                @foreach($struktur_belanja as $bidangKey => $bidang)
                <tr>
                    <td>{{ $bidang['kode'] }}</td>
                    <td class="font-bold">{{ $bidang['nama'] }}</td>
                    <td class="text-right font-bold">{{ number_format($bidang['total'], 2, ',', '.') }}</td>
                    <td>{{ $bidang['keterangan'] ?? '' }}</td>
                </tr>
                @php
                    $displayed_categories[] = strtolower($bidang['nama']);
                @endphp

                <!-- Kegiatan dalam Bidang -->
                @foreach($bidang['kegiatan'] as $kegiatanKey => $kegiatan)
                <tr>
                    <td>{{ $kegiatan['kode'] }}</td>
                    <td class="indent-1">{{ $kegiatan['nama'] }}</td>
                    <td class="text-right">{{ number_format($kegiatan['total'], 2, ',', '.') }}</td>
                    <td>{{ $kegiatan['keterangan'] ?? '' }}</td>
                </tr>
                @endforeach
                @endforeach

                <!-- Ensure specific categories are displayed -->
                @if(!in_array('belanja barang/jasa', $displayed_categories))
                <tr>
                    <td>2.1.</td>
                    <td class="font-bold">Belanja Barang/Jasa</td>
                    <td class="text-right font-bold">0,00</td>
                    <td>-</td>
                </tr>
                @endif

                @if(!in_array('belanja modal', $displayed_categories))
                <tr>
                    <td>2.2.</td>
                    <td class="font-bold">Belanja Modal</td>
                    <td class="text-right font-bold">0,00</td>
                    <td>-</td>
                </tr>
                @endif

                @if(!in_array('belanja tak terduga', $displayed_categories))
                <tr>
                    <td>2.3.</td>
                    <td class="font-bold">Belanja Tak Terduga</td>
                    <td class="text-right font-bold">0,00</td>
                    <td>-</td>
                </tr>
                @endif

                <!-- JUMLAH BELANJA -->
                <tr class="bg-gray">
                    <td></td>
                    <td class="font-bold">JUMLAH BELANJA</td>
                    <td class="text-right font-bold">{{ number_format($total_belanja, 2, ',', '.') }}</td>
                    <td></td>
                </tr>

                <!-- SURPLUS / (DEFISIT) -->
                <tr class="bg-gray">
                    <td></td>
                    <td class="font-bold">
                        SURPLUS / (DEFISIT) 
                    </td>
                    <td class="text-right font-bold">{{ number_format($surplus_defisit, 2, ',', '.') }}</td>
                    <td>
                        @if($surplus_defisit > 0)
                            Surplus
                        @elseif($surplus_defisit < 0)
                            Defisit
                        @else
                            Tidak Ada Surplus/Defisit
                        @endif
                    </td>
                </tr>

                {{-- PEMBIAYAAN - SECTION REMOVED --}}
                {{--
                <tr>
                    <td class="font-bold">3.</td>
                    <td class="font-bold">PEMBIAYAAN</td>
                    <td></td>
                    <td></td>
                </tr>

                <!-- 3.1. Penerimaan Pembiayaan -->
                <tr>
                    <td>3.1.</td>
                    <td class="font-bold">Penerimaan Pembiayaan</td>
                    <td class="text-right font-bold">{{ number_format($total_penerimaan_pembiayaan, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
                @foreach($pembiayaan['penerimaan'] as $index => $item)
                <tr>
                    <td>{{ $item['kode'] }}</td>
                    <td class="indent-1">{{ $item['uraian'] }}</td>
                    <td class="text-right">{{ number_format($item['jumlah'], 2, ',', '.') }}</td>
                    <td>{{ $item['keterangan'] ?? '' }}</td>
                </tr>
                @endforeach

                <!-- 3.2. Pengeluaran Pembiayaan -->
                <tr>
                    <td>3.2.</td>
                    <td class="font-bold">Pengeluaran Pembiayaan</td>
                    <td class="text-right font-bold">{{ number_format($total_pengeluaran_pembiayaan, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
                @foreach($pembiayaan['pengeluaran'] as $index => $item)
                <tr>
                    <td>{{ $item['kode'] }}</td>
                    <td class="indent-1">{{ $item['uraian'] }}</td>
                    <td class="text-right">{{ number_format($item['jumlah'], 2, ',', '.') }}</td>
                    <td>{{ $item['keterangan'] ?? '' }}</td>
                </tr>
                @endforeach

                <!-- JUMLAH PEMBIAYAAN -->
                <tr class="bg-gray">
                    <td></td>
                    <td class="font-bold">JUMLAH PEMBIAYAAN (Selisih Penerimaan dan Pengeluaran)</td>
                    <td class="text-right font-bold">{{ number_format($selisih_pembiayaan, 2, ',', '.') }}</td>
                    <td></td>
                </tr>

                <!-- SISA LEBIH / (KURANG) PERHITUNGAN ANGGARAN -->
                <tr class="bg-gray">
                    <td></td>
                    <td class="font-bold">SISA LEBIH / (KURANG) PERHITUNGAN ANGGARAN (SILPA / SIKPA)</td>
                    <td class="text-right font-bold">{{ number_format($sisa_anggaran, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
                --}}
            </tbody>
        </table>
    </div>
    <div class="signature-section">
        <table>
            <tr>
                <td style="width: 50%;">
                    <p>&nbsp;</p>
                    
                </td>
                <td style="width: 50%;">
                    <p>&nbsp;</p>
                    <p>{{ config('desa.nama_desa') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
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