<?php

use App\Models\Surat;
use App\Models\Penduduk;
use App\Enums\JenisKelamin;
use App\Enums\Agama;
use App\Enums\StatusPerkawinan;
use Carbon\Carbon;

// PASTIKAN JANGAN HAPUS TABEL SAYA

test('surat model has correct fillable attributes', function () {
    $surat = new Surat();
    $fillable = [
        'nomor_surat', 'jenis_surat', 'tanggal_pengajuan', 'tanggal_disetujui', 'nik_pemohon', 'keperluan', 'status_surat', 'catatan', 'attachment_bukti_pendukung',
        'nik_penduduk_meninggal', 'tanggal_kematian', 'waktu_kematian', 'tempat_kematian', 'penyebab_kematian',
        'alamat_tujuan', 'rt_tujuan', 'rw_tujuan', 'kelurahan_desa_tujuan', 'kecamatan_tujuan', 'kabupaten_kota_tujuan', 'provinsi_tujuan', 'alasan_pindah', 'klasifikasi_pindah', 'data_pengikut_pindah',
        'nama_bayi', 'tempat_dilahirkan', 'tempat_kelahiran', 'tanggal_lahir_bayi', 'waktu_lahir_bayi', 'jenis_kelamin_bayi', 'jenis_kelahiran', 'anak_ke', 'penolong_kelahiran', 'berat_bayi_kg', 'panjang_bayi_cm', 'nik_penduduk_ibu', 'nama_ibu', 'umur_ibu_saat_kelahiran', 'nik_penduduk_ayah', 'nama_ayah', 'umur_ayah_saat_kelahiran',
        'nama_usaha', 'jenis_usaha', 'alamat_usaha', 'status_bangunan_usaha', 'perkiraan_modal_usaha', 'perkiraan_pendapatan_usaha', 'jumlah_tenaga_kerja', 'sejak_tanggal_usaha',
        'penghasilan_perbulan_kepala_keluarga', 'pekerjaan_kepala_keluarga', 'nik_penduduk_siswa', 'nama_sekolah', 'nisn_siswa', 'kelas_siswa',
        'nomor_ktp_hilang', 'tanggal_perkiraan_hilang', 'lokasi_perkiraan_hilang', 'kronologi_singkat', 'nomor_laporan_polisi', 'tanggal_laporan_polisi',
    ];
    expect($surat->getFillable())->toBe($fillable);
});

test('surat model has correct casts', function () {
    $surat = new Surat();
    $expectedCasts = [
        'attachment_bukti_pendukung' => 'array',
        'tanggal_pengajuan' => 'date',
        'tanggal_disetujui' => 'date',
        'tanggal_kematian' => 'date',
        'tanggal_lahir_bayi' => 'date',
        'sejak_tanggal_usaha' => 'date',
        'tanggal_perkiraan_hilang' => 'date',
        'tanggal_laporan_polisi' => 'date',
        'data_pengikut_pindah' => 'array',
        'berat_bayi_kg' => 'decimal:2',
        'panjang_bayi_cm' => 'decimal:2',
        'perkiraan_modal_usaha' => 'integer',
        'perkiraan_pendapatan_usaha' => 'integer',
        'jumlah_tenaga_kerja' => 'integer',
        'penghasilan_perbulan_kepala_keluarga' => 'integer',
        'pekerjaan_kepala_keluarga' => 'string',
        'anak_ke' => 'integer',
        'umur_ibu_saat_kelahiran' => 'integer',
        'umur_ayah_saat_kelahiran' => 'integer',
    ];
    $actualCasts = $surat->getCasts();
    // Hilangkan kunci yang tidak relevan (id_surat, deleted_at)
    unset($actualCasts['id_surat'], $actualCasts['deleted_at']);
    expect($actualCasts)->toMatchArray($expectedCasts);
});

test('surat model can generate nomor surat sesuai format', function () {
    $jenisSurat = 'SK_KEMATIAN';
    $nomor = Surat::generateNomorSurat($jenisSurat);
    if (!preg_match('/^472.12\/\d{3}\/KMT\/BTJR-TMR\/[IVXLCDM]+\/[0-9]{4}$/', $nomor)) {
        fwrite(STDERR, "\nNomor surat yang dihasilkan: $nomor\n");
    }
    expect($nomor)->toMatch('/^472.12\/\d{3}\/KMT\/BTJR-TMR\/[IVXLCDM]+\/[0-9]{4}$/');
});

test('surat approve and reject logic', function () {
    // Buat dummy penduduk
    $nik = '3271123456789012';
    $penduduk = Penduduk::create([
        'nik' => $nik,
        'nama' => 'Test User',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1990-01-01',
        'jenis_kelamin' => JenisKelamin::LakiLaki,
        'alamat' => 'Jl. Test',
        'rt' => '001',
        'rw' => '002',
        'desa_kelurahan' => 'Batujajar Timur',
        'kecamatan' => 'Batujajar',
        'kabupaten_kota' => 'Kabupaten Bandung Barat',
        'provinsi' => 'Jawa Barat',
        'kode_pos' => '40561',
        'agama' => Agama::Islam,
        'status_perkawinan' => StatusPerkawinan::BelumMenikah,
        'pekerjaan' => 'Pelajar / Mahasiswa',
        'kewarganegaraan' => 'WNI',
        'pendidikan' => 'S1',
        'no_kk' => '3271123456789000',
    ]);

    $surat = Surat::create([
        'jenis_surat' => 'SK_KEMATIAN',
        'nik_pemohon' => $nik,
        'keperluan' => 'Keperluan Test',
        'status_surat' => 'Diajukan',
    ]);

    // Approve
    $result = $surat->approve();
    $surat->refresh();
    expect($result)->toBeTrue()
        ->and($surat->status_surat)->toBe('Disetujui')
        ->and($surat->nomor_surat)->not->toBeNull();

    // Reject
    $surat->status_surat = 'Diajukan';
    $surat->save();
    $resultReject = $surat->reject();
    $surat->refresh();
    expect($resultReject)->toBeTrue()
        ->and($surat->status_surat)->toBe('Ditolak');

    // Cleanup
    $surat->delete();
    $penduduk->delete();
});

test('surat model relasi ke penduduk', function () {
    $nik = '3271123456789013';
    $penduduk = Penduduk::create([
        'nik' => $nik,
        'nama' => 'Relasi User',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-05-05',
        'jenis_kelamin' => JenisKelamin::Perempuan,
        'alamat' => 'Jl. Relasi',
        'rt' => '003',
        'rw' => '004',
        'desa_kelurahan' => 'Batujajar Timur',
        'kecamatan' => 'Batujajar',
        'kabupaten_kota' => 'Kabupaten Bandung Barat',
        'provinsi' => 'Jawa Barat',
        'kode_pos' => '40561',
        'agama' => Agama::Islam,
        'status_perkawinan' => StatusPerkawinan::BelumMenikah,
        'pekerjaan' => 'Pelajar / Mahasiswa',
        'kewarganegaraan' => 'WNI',
        'pendidikan' => 'S1',
        'no_kk' => '3271123456789001',
    ]);

    $surat = Surat::create([
        'jenis_surat' => 'SK_KEMATIAN',
        'nik_pemohon' => $nik,
        'keperluan' => 'Test Relasi',
        'status_surat' => 'Diajukan',
    ]);

    $surat->refresh();
    expect($surat->pemohon)->not->toBeNull()
        ->and($surat->pemohon->nik)->toBe($nik)
        ->and($surat->nama_pemohon)->toBe('Relasi User');

    // Cleanup
    $surat->delete();
    $penduduk->delete();
}); 