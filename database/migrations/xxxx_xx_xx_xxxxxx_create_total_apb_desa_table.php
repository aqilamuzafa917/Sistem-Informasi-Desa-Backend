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
        Schema::create('total_apb_desa', function (Blueprint $table) {
            $table->id('id_total');
            $table->year('tahun_anggaran')->unique();
            $table->decimal('total_pendapatan', 15, 2);
            $table->decimal('total_belanja', 15, 2);
            $table->decimal('saldo_sisa', 15, 2);
            $table->date('tanggal_pelaporan');
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
        Schema::dropIfExists('total_apb_desa');
    }
};