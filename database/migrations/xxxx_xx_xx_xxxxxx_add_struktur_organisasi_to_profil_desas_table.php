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
        Schema::table('profil_desas', function (Blueprint $table) {
            // Add the new column after 'alamat_kantor' (or adjust as needed)
            // Use json type if your database supports it (recommended)
            $table->json('struktur_organisasi')->nullable()->after('alamat_kantor');
            // Alternatively, use text type if json is not supported:
            // $table->text('struktur_organisasi')->nullable()->after('alamat_kantor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_desas', function (Blueprint $table) {
            $table->dropColumn('struktur_organisasi');
        });
    }
};