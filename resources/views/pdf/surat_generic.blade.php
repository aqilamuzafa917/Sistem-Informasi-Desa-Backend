<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan - {{ $surat->jenis_surat }}</title>
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
            @if(optional($surat->pemohon)->tempat_lahir || optional($surat->pemohon)->tanggal_lahir)
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->tempat_lahir ?? '....................' }}, {{ optional($surat->pemohon)->tanggal_lahir ? \Carbon\Carbon::parse($surat->pemohon->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '.... ................... 20...' }}</td>
            </tr>
            @endif
            @if(optional($surat->pemohon)->kewarganegaraan || optional($surat->pemohon)->agama)
            <tr>
                <td>Kewarganegaraan/Agama</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->kewarganegaraan ?? '..................................................' }} / {{ optional($surat->pemohon)->agama ?? '..................................................' }}</td>
            </tr>
            @endif
            @if(optional($surat->pemohon)->status_perkawinan)
            <tr>
                <td>Status Perkawinan</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->status_perkawinan ?? '..................................................' }}</td>
            </tr>
            @endif
            @if(optional($surat->pemohon)->pekerjaan)
            <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td>{{ optional($surat->pemohon)->pekerjaan ?? '..................................................' }}</td>
            </tr>
            @endif
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    RT. {{ optional($surat->pemohon)->rt ?? '00' }} RW. {{ optional($surat->pemohon)->rw ?? '00' }}<br>
                    Desa {{ optional($surat->pemohon)->desa_kelurahan ?? config('desa.nama_desa') ?? '....................' }} Kecamatan {{ optional($surat->pemohon)->kecamatan ?? config('desa.nama_kecamatan') ?? '....................' }}
                </td>
            </tr>
        </table>

        <p style="text-align: justify; margin-top: 15px;">Berdasarkan data dan keterangan yang ada, dengan ini menerangkan bahwa orang tersebut memiliki data sebagai berikut:</p>
        
        <table class="info-table" style="margin-left: 20px;">
            @foreach ($surat->toArray() as $key => $value)
                @if (!is_null($value) && $value !== '' && !in_array($key, ['id_surat', 'created_at', 'updated_at', 'nomor_surat', 'jenis_surat', 'status_surat', 'tanggal_disetujui', 'tanggal_pengajuan', 'nik_pemohon']))
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