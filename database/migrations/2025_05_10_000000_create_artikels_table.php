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
        Schema::create('artikels', function (Blueprint $table) {
            $table->id('id_artikel');
            $table->enum('jenis_artikel', ['resmi', 'warga'])->comment('Jenis artikel: resmi dari desa atau warga');
            $table->enum('status_artikel', ['diajukan', 'ditolak', 'disetujui'])->default('diajukan')->comment('Status persetujuan artikel');
            $table->string('judul_artikel');
            $table->string('kategori_artikel')->comment('Kategori artikel: kegiatan sosial, berita, label, dll');
            $table->text('isi_artikel')->comment('Konten utama artikel');
            $table->string('penulis_artikel');
            $table->date('tanggal_kejadian_artikel')->nullable()->comment('Tanggal kejadian yang diberitakan');
            $table->timestamp('tanggal_publikasi_artikel')->nullable()->comment('Tanggal artikel dipublikasikan');
            
            // Lokasi untuk Leaflet
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            
            $table->json('media_artikel')->nullable()->comment('Media pendukung: foto, video, attachment dalam format JSON');
            $table->timestamps();
            
            // Indeks untuk performa query
            $table->index('jenis_artikel');
            $table->index('status_artikel');
            $table->index('kategori_artikel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};