<?php


$config = [
    // Informasi Instansi (Desa/Kelurahan)
    'nama_kabupaten'     => 'BANDUNG BARAT', // Tulis tanpa "Kabupaten" jika di kop sudah ada
    'nama_kecamatan'     => 'BATUJAJAR',     // Tulis tanpa "Kecamatan" jika di kop sudah ada
    'nama_desa'          => 'BATUJAJAR TIMUR', // Atau 'nama_kelurahan'
    'alamat_desa'        => 'Jl. Raya Batujajar No.191, Batujajar Tim., Kec. Batujajar, Kabupaten Bandung Barat, Jawa Barat 40561',
    'kode_pos'           => '40561', // Opsional, jika diperlukan terpisah

    // Informasi Pejabat Utama (Kepala Desa/Lurah)
    'jabatan_kepala'     => 'KEPALA DESA', // Bisa juga 'LURAH'
    'nama_kepala_desa'   => 'NAMA KEPALA DESA DI SINI', // Nama lengkap Kepala Desa/Lurah

    // Informasi Pejabat Penandatangan (Bisa Kepala Desa, Sekdes, dll.)
    // Ini yang akan muncul di bagian TTD
    'jabatan_ttd'        => 'KEPALA DESA', // Defaultnya Kepala Desa. Bisa diubah menjadi 'SEKRETARIS DESA', 'Plt. KEPALA DESA', atau 'SEKRETARIS DESA a.n KEPALA DESA'
    'nama_pejabat_ttd'   => 'NAMA PEJABAT PENANDATANGAN', // Nama yang akan tercetak di bawah TTD
    'nip_pejabat_ttd'    => '19XXXXXXXXXXXXXX', // NIP pejabat penandatangan (jika ada, biarkan kosong '' atau null jika tidak ada)

    // Path Logo
    // Sesuaikan path ini agar bisa diakses oleh library PDF generator Anda.
    // Mungkin perlu path absolut atau fungsi helper seperti public_path() atau asset().
    // Menggunakan base64 embed seringkali lebih aman untuk PDF.
    'logo_path'          => public_path('images/logo-kbb.png'), // Contoh menggunakan helper Laravel
    // 'logo_base64'     => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...' // Alternatif jika menggunakan base64

    // Informasi Tambahan (Opsional)
    'website_desa'       => 'https://batujajartimur.desa.id', // Jika ada
    'email_desa'         => 'info@batujajartimur.desa.id',   // Jika ada
    'telepon_desa'       => '(022) 68XXXXX',               // Jika ada
];

// Kemudian di Controller, saat merender view:
// return view('nama_view_surat', ['surat' => $dataSurat, 'config' => $config]);

// Atau jika menggunakan library PDF seperti DomPDF:
// $pdf = PDF::loadView('nama_view_surat', ['surat' => $dataSurat, 'config' => $config]);
// return $pdf->stream('surat_keterangan.pdf');

?>