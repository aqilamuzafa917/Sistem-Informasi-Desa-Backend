<?php


$config = [
    'kode' => 'BTJR-TMR', // <-- Ganti dengan kode desa/instansi Anda yang sebenarnya
    // Informasi Instansi (Desa/Kelurahan)
    'nama_kabupaten'     => 'Bandung Barat', // Tulis tanpa "Kabupaten" jika di kop sudah ada
    'nama_kecamatan'     => 'Batujajar',     // Tulis tanpa "Kecamatan" jika di kop sudah ada
    'nama_desa'          => 'Batujajar Timur', // Atau 'nama_kelurahan'
    'alamat_desa'        => 'Jl. Raya Batujajar No.191, Batujajar Timur, Kec. Batujajar, Kabupaten Bandung Barat, Jawa Barat 40561',
    'kode_pos'           => '40561', // Opsional, jika diperlukan terpisah
    'nama_provinsi'       => 'Jawa Barat',
    // Informasi Pejabat Utama (Kepala Desa/Lurah)
    'jabatan_kepala'     => 'Kepala Desa', // Bisa juga 'LURAH'
    'nama_kepala_desa'   => 'Nama Kepala Desa', // Nama lengkap Kepala Desa/Lurah

    // Informasi Pejabat Penandatangan (Bisa Kepala Desa, Sekdes, dll.)
    // Ini yang akan muncul di bagian TTD
    'jabatan_ttd'        => 'Jabatan Tandatangan', // Defaultnya Kepala Desa. Bisa diubah menjadi 'SEKRETARIS DESA', 'Plt. KEPALA DESA', atau 'SEKRETARIS DESA a.n KEPALA DESA'
    'nama_pejabat_ttd'   => 'Nama Pejabat Tandatangan', // Nama yang akan tercetak di bawah TTD
    'nip_pejabat_ttd'    => '19XXXXXXXXXXXXXXttd', // NIP pejabat penandatangan (jika ada, biarkan kosong '' atau null jika tidak ada)

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

return $config; // Tambahkan baris ini