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
        Schema::create('profil_desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa')->unique(); // Nama desa sebagai pengenal unik
            $table->text('sejarah')->nullable();
            $table->text('tradisi_budaya')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->string('peta_lokasi')->nullable(); // Bisa berupa URL gambar peta atau data koordinat
            $table->text('alamat_kantor')->nullable();
            $table->json('struktur_organisasi')->nullable();
            $table->json('batas_wilayah')->nullable()->comment('Struktur: {"utara": "string", "timur": "string", "selatan": "string", "barat": "string"}');
            $table->bigInteger('luas_desa')->nullable()->comment('Luas desa dalam meter persegi');
            $table->json('social_media')->nullable()->comment('Struktur: [{"platform": "string", "url": "string", "username": "string"}]');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_desas');
    }
};