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
        Schema::create('realisasi_belanja', function (Blueprint $table) {
            $table->id('id_belanja');
            $table->year('tahun_anggaran');
            $table->date('tanggal_realisasi');
            $table->enum('kategori', ['Belanja Barang/Jasa', 'Belanja Modal', 'Belanja Tak Terduga']);
            $table->text('deskripsi');
            $table->decimal('jumlah', 15, 2);
            $table->string('penerima_vendor');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi_belanja');
    }
};