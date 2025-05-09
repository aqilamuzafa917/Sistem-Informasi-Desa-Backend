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
            padding: 5px 0; /* Penyesuaian: padding vertikal ditambah dari 2px menjadi 5px */
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
        @foreach ($surat->toArray() as $key => $value)
            @if (!is_null($value) && $value !== '' && !in_array($key, ['id_surat', 'created_at', 'updated_at', 'nomor_surat']))
                <tr>
                    <td width="200" style="vertical-align: top;">
                        @php
                            $displayKey = str_replace('_', ' ', $key);
                            $displayKey = ucwords($displayKey);
                            // Menyesuaikan kapitalisasi untuk akronim umum
                            $displayKey = preg_replace_callback('/\b(Nik|Ktp|Rt|Rw|Nisn|Sk|Id|No)\b/i', function($matches) {
                                return strtoupper($matches[0]);
                            }, $displayKey);
                        @endphp
                        {{ $displayKey }}
                    </td>
                    <td width="10" style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">
                        @if ($value instanceof \BackedEnum)
                            {{ $value->value }}
                        @elseif ($value instanceof \UnitEnum && !($value instanceof \BackedEnum)) {{-- Untuk enum yang tidak didukung (non-backed) --}}
                            {{ $value->name }}
                        @elseif (is_array($value) || is_object($value))
                            {{-- Untuk data array atau objek kompleks (bukan Enum), tampilkan sebagai JSON --}}
                            <pre style="font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; white-space: pre-wrap;">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @elseif (str_contains(strtolower($key), 'tanggal') || str_contains(strtolower($key), 'waktu'))
                            @php
                                $formattedDate = $value; // Default ke nilai asli
                                try {
                                    // Coba parse jika formatnya adalah timestamp penuh atau format tanggal umum
                                    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) || preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                                        $carbonDate = \Carbon\Carbon::parse($value);
                                        if (str_contains(strtolower($key), 'waktu') || $key === 'tanggal_request') {
                                            $formattedDate = $carbonDate->isoFormat('D MMMM YYYY, HH:mm:ss');
                                        } else {
                                            $formattedDate = $carbonDate->isoFormat('D MMMM YYYY');
                                        }
                                    }
                                } catch (\Exception $e) {
                                    // Jika parsing gagal, biarkan nilai asli
                                }
                            @endphp
                            {{ $formattedDate }}
                        @elseif ($key === 'berat_bayi_kg')
                            {{ $value }} kg
                        @elseif ($key === 'panjang_bayi_cm')
                            {{ $value }} cm
                        @elseif (in_array($key, ['perkiraan_modal_usaha', 'perkiraan_pendapatan_usaha', 'penghasilan_perbulan_kepala_keluarga']))
                            Rp {{ number_format((float)$value, 0, ',', '.') }}
                        @elseif (is_bool($value))
                            {{ $value ? 'Ya' : 'Tidak' }}
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    </table>

    <p>Demikian surat keterangan ini kami buat dengan sebenarnya, kepada pihak yang berkepentingan agar menjadi tahu serta maklum hendaknya.</p>

    <div class="footer">
        <p>Batujajar Timur, {{ date('d F Y') }}</p>
        <p>Kepala Desa Batujajar Timur</p>
        
        <div class="signature"></div>
    </div>
</body>
</html>