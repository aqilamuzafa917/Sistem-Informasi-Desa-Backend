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
            $table->string('indikator_idm');
            $table->integer('skor');
            $table->string('keterangan')->nullable();
            $table->string('kegiatan')->nullable();
            $table->float('nilai_plus', 5, 3)->nullable();
            $table->json('pelaksana')->nullable();
            $table->enum('kategori', ['A', 'B', 'C', 'D'])->default('D');
            $table->integer('tahun');

            // Foreign key constraint
            $table->foreign('indikator_idm')
                ->references('nama_indikator')
                ->on('indikator_idm')
                ->onDelete('cascade');

            // Indexes
            $table->index(['indikator_idm']);
            $table->index(['tahun']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variabel_idm');
    }
};
