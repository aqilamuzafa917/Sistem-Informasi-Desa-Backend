<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surats', function (Blueprint $table) {
            // Kolom Umum
            $table->id('id_surat'); // Mengganti nama default 'id' menjadi 'id_surat'
            $table->string('nomor_surat')->nullable()->comment('Nomor surat resmi, bisa diisi setelah approval');
            $table->string('jenis_surat')->comment('e.g., SK_DOMISILI, SK_KEMATIAN');
            $table->timestamp('tanggal_pengajuan')->nullable()->comment('Tanggal permintaan surat dibuat');
            $table->date('tanggal_disetujui')->nullable()->comment('Tanggal surat disetujui/diterbitkan');
            $table->string('nik_pemohon')->comment('NIK pemohon sebagai FK ke tabel penduduk - untuk mengambil nama, tempat/tanggal lahir, dan jenis kelamin dari tabel penduduk');
            // Kolom berikut dapat dihapus karena seharusnya diambil dari tabel penduduk berdasarkan NIK
            // Dipertahankan untuk sementara dengan flag nullable dan comment yang jelas
            // $table->string('nama_pemohon')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_pemohon'); 
            // $table->enum('jenis_kelamin_pemohon', ['Laki-laki', 'Perempuan'])->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_pemohon');
            // $table->string('tempat_lahir_pemohon')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_pemohon');
            // $table->date('tanggal_lahir_pemohon')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_pemohon');
            $table->string('keperluan')->comment('Tujuan pembuatan surat');
            $table->enum('status_surat', ['Draft', 'Pending', 'Approved', 'Rejected', 'Printed'])->default('Pending');
            $table->string('catatan')->nullable()->comment('Catatan tambahan untuk petugas');
            $table->timestamps(); // created_at dan updated_at
            // Kolom baru untuk attachment bukti pendukung
            $table->string('attachment_bukti_pendukung')->nullable()->comment('Path atau nama file attachment');

            // Kolom Spesifik SK Kematian (nullable)
            $table->string('nik_penduduk_meninggal')->nullable()->comment('NIK penduduk yang meninggal, FK ke tabel penduduk - untuk mengambil nama, tempat/tanggal lahir, dan jenis kelamin dari tabel penduduk');
            // Kolom berikut dapat dihapus karena seharusnya diambil dari tabel penduduk berdasarkan NIK
            // Dipertahankan untuk sementara dengan flag nullable dan comment yang jelas
            // $table->string('nama_meninggal')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_penduduk_meninggal');
            // $table->string('nik_meninggal')->nullable()->comment('Data sementara: duplikasi dari nik_penduduk_meninggal dalam format string');
            // $table->string('tempat_lahir_meninggal')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_penduduk_meninggal');
            // $table->date('tanggal_lahir_meninggal')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_penduduk_meninggal');
            // $table->enum('jenis_kelamin_meninggal', ['Laki-laki', 'Perempuan'])->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_penduduk_meninggal');
            $table->string('alamat_terakhir_meninggal')->nullable()->comment('Alamat terakhir penduduk yang meninggal sesuai data kependudukan');
            $table->date('tanggal_kematian')->nullable();
            $table->time('waktu_kematian')->nullable();
            $table->string('tempat_kematian')->nullable();
            $table->string('penyebab_kematian')->nullable();
            $table->string('hubungan_pelapor_kematian')->nullable()->comment('Hubungan pelapor dengan penduduk yang meninggal');

            // Kolom Spesifik SK Pindah (nullable)
            $table->string('alamat_tujuan')->nullable()->comment('Alamat tujuan pindah untuk pembaruan data kependudukan');
            $table->string('rt_tujuan')->nullable()->comment('RT tujuan pindah untuk pembaruan data kependudukan');
            $table->string('rw_tujuan')->nullable()->comment('RW tujuan pindah untuk pembaruan data kependudukan');
            $table->string('kelurahan_desa_tujuan')->nullable()->comment('Kelurahan/Desa tujuan pindah untuk pembaruan data kependudukan');
            $table->string('kecamatan_tujuan')->nullable()->comment('Kecamatan tujuan pindah untuk pembaruan data kependudukan');
            $table->string('kabupaten_kota_tujuan')->nullable()->comment('Kabupaten/Kota tujuan pindah untuk pembaruan data kependudukan');
            $table->string('provinsi_tujuan')->nullable()->comment('Provinsi tujuan pindah untuk pembaruan data kependudukan');
            $table->string('alasan_pindah')->nullable();
            $table->string('klasifikasi_pindah')->nullable()->comment('Klasifikasi pindah (antar desa, kecamatan, kabupaten, provinsi)');
            $table->json('data_pengikut_pindah')->nullable()->comment('Data pengikut pindah dalam format JSON, berisi NIK dan data kependudukan anggota keluarga');

            // Kolom Spesifik SK Kelahiran (nullable)
            $table->string('nama_bayi')->nullable()->comment('Nama lengkap bayi untuk pendaftaran data kependudukan baru');
            $table->string('tempat_dilahirkan')->nullable()->comment('Tempat dilahirkan (Rumah Sakit/Puskesmas/Rumah/dll)');
            $table->string('tempat_kelahiran')->nullable()->comment('Kota/Kabupaten kelahiran untuk data kependudukan');
            $table->date('tanggal_lahir_bayi')->nullable()->comment('Tanggal lahir bayi untuk data kependudukan');
            $table->time('waktu_lahir_bayi')->nullable();
            $table->enum('jenis_kelamin_bayi', ['Laki-laki', 'Perempuan'])->nullable()->comment('Jenis kelamin bayi untuk data kependudukan');
            $table->string('jenis_kelahiran')->nullable()->comment('Jenis kelahiran (Tunggal, Kembar 2, dll) untuk data kependudukan');
            $table->integer('anak_ke')->nullable()->comment('Urutan kelahiran dalam keluarga');
            $table->string('penolong_kelahiran')->nullable();
            $table->decimal('berat_bayi_kg', 4, 2)->nullable()->comment('Berat bayi dalam kg untuk data kesehatan kependudukan');
            $table->decimal('panjang_bayi_cm', 5, 2)->nullable()->comment('Panjang bayi dalam cm untuk data kesehatan kependudukan');
            $table->string('nik_penduduk_ibu')->nullable()->comment('NIK ibu bayi, FK ke tabel penduduk');
            $table->string('nik_penduduk_ayah')->nullable()->comment('NIK ayah bayi, FK ke tabel penduduk');
            $table->string('nik_penduduk_pelapor_lahir')->nullable()->comment('NIK pelapor kelahiran, FK ke tabel penduduk');
            $table->string('hubungan_pelapor_lahir')->nullable()->comment('Hubungan pelapor dengan bayi');

            // Kolom Spesifik SK Usaha (nullable)
            $table->string('nama_usaha')->nullable();
            $table->string('jenis_usaha')->nullable();
            $table->string('alamat_usaha')->nullable()->comment('Alamat usaha untuk pendataan ekonomi kependudukan');
            $table->string('status_bangunan_usaha')->nullable();
            $table->bigInteger('perkiraan_modal_usaha')->nullable();
            $table->bigInteger('perkiraan_pendapatan_usaha')->nullable();
            $table->integer('jumlah_tenaga_kerja')->nullable()->comment('Jumlah tenaga kerja untuk pendataan ekonomi kependudukan');
            $table->date('sejak_tanggal_usaha')->nullable();

            // Kolom Spesifik Rekom KIS / KIP / SKTM (nullable)
            $table->bigInteger('penghasilan_perbulan_kepala_keluarga')->nullable()->comment('Penghasilan per bulan untuk pendataan ekonomi kependudukan');
            $table->string('pekerjaan_kepala_keluarga')->nullable()->comment('Pekerjaan kepala keluarga sesuai kode klasifikasi pekerjaan');
            $table->string('nik_penduduk_siswa')->nullable()->comment('NIK siswa untuk KIP, FK ke tabel penduduk - untuk mengambil nama, tempat/tanggal lahir, dan jenis kelamin dari tabel penduduk');
            // Kolom berikut dapat dihapus karena seharusnya diambil dari tabel penduduk berdasarkan NIK
            // Dipertahankan untuk sementara dengan flag nullable dan comment yang jelas
            // $table->string('nama_siswa')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_penduduk_siswa');
            // $table->string('tempat_lahir_siswa')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_penduduk_siswa');
            // $table->date('tanggal_lahir_siswa')->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_penduduk_siswa');
            // $table->enum('jenis_kelamin_siswa', ['Laki-laki', 'Perempuan'])->nullable()->comment('Data sementara: seharusnya diambil dari tabel penduduk berdasarkan nik_penduduk_siswa');
            $table->string('nama_sekolah')->nullable();
            $table->string('nisn_siswa')->nullable();
            $table->string('kelas_siswa')->nullable();

            // Kolom Spesifik SK Kehilangan KTP (nullable)
            $table->string('nomor_ktp_hilang')->nullable()->comment('Nomor KTP yang hilang sesuai data kependudukan');
            $table->date('tanggal_perkiraan_hilang')->nullable();
            $table->string('lokasi_perkiraan_hilang')->nullable();
            $table->string('kronologi_singkat')->nullable();
            $table->string('nomor_laporan_polisi')->nullable();
            $table->date('tanggal_laporan_polisi')->nullable();

            // Indeks (opsional, tambahkan jika diperlukan untuk performa query)
            $table->index('jenis_surat');
            $table->index('nik_pemohon');
            $table->index('status_surat');

            // Foreign key constraints untuk data kependudukan
           
            // PENTING: Semua data demografi (nama, tempat/tanggal lahir, jenis kelamin) 
            // seharusnya diambil dari tabel penduduk berdasarkan NIK
            // dan tidak perlu disimpan di tabel surat ini untuk menghindari redundansi data
        
            // FK untuk data pemohon
            $table->foreign('nik_pemohon')->references('nik')->on('penduduk')->onDelete('cascade')
                  ->comment('FK untuk mengambil data demografi pemohon (nama, TTL, jenis kelamin) dari tabel penduduk');
            
            // FK untuk data penduduk yang meninggal
            $table->foreign('nik_penduduk_meninggal')->references('nik')->on('penduduk')->onDelete('set null')
                  ->comment('FK untuk mengambil data demografi (nama, TTL, jenis kelamin) penduduk yang meninggal');
            
            // FK untuk data kelahiran (orang tua)
            $table->foreign('nik_penduduk_ibu')->references('nik')->on('penduduk')->onDelete('set null')
                  ->comment('FK untuk mengambil data demografi (nama, TTL, jenis kelamin) ibu dari bayi');
            $table->foreign('nik_penduduk_ayah')->references('nik')->on('penduduk')->onDelete('set null')
                  ->comment('FK untuk mengambil data demografi (nama, TTL, jenis kelamin) ayah dari bayi');
            $table->foreign('nik_penduduk_pelapor_lahir')->references('nik')->on('penduduk')->onDelete('set null')
                  ->comment('FK untuk mengambil data demografi (nama, TTL, jenis kelamin) pelapor kelahiran');
            
            // FK untuk data siswa
            $table->foreign('nik_penduduk_siswa')->references('nik')->on('penduduk')->onDelete('set null')
                  ->comment('FK untuk mengambil data demografi (nama, TTL, jenis kelamin) siswa untuk KIP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};