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
        Schema::create('variabel_idm', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('indikator_idm');
            $table->integer('skor');
            $table->string('keterangan');
            $table->string('kegiatan');
            $table->float('nilai_plus')->nullable();
            $table->json('pelaksana')->nullable();
            $table->enum('kategori', ['IKL', 'IKE', 'IKS']);
            $table->integer('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variabel_i_d_m_s');
    }
};
