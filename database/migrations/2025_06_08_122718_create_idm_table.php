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
            $table->integer('tahun')->unique();
            $table->float('skor_idm', 5, 3);
            $table->string('status_idm');
            $table->string('target_status')->nullable();
            $table->float('skor_minimal', 5, 3)->nullable();
            $table->float('penambahan')->nullable();
            $table->json('komponen')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tahun']);
            $table->index(['skor_idm']);
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
