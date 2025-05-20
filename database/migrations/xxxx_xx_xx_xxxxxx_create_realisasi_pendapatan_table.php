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
        Schema::create('realisasi_pendapatan', function (Blueprint $table) {
            $table->id('id_pendapatan');
            $table->year('tahun_anggaran');
            $table->date('tanggal_realisasi');
            $table->enum('kategori', ['Pendapatan Asli Desa', 'Pendapatan Transfer', 'Pendapatan Lain-lain']);
            $table->string('sub_kategori');
            $table->text('deskripsi');
            $table->decimal('jumlah', 15, 2);
            $table->string('sumber_dana');
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
        Schema::dropIfExists('realisasi_pendapatan');
    }
};