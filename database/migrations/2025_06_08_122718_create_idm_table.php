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
        Schema::create('idm', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->float('skor_idm');
            $table->string('status_idm');
            $table->string('target_status');
            $table->float('skor_minimal');
            $table->float('penambahan');
            $table->json('komponen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idm');
    }
};
