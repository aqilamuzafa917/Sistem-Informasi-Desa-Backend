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
        Schema::table('surats', function (Blueprint $table) {
            // Tambahkan kolom catatan_admin setelah kolom status (atau sesuaikan posisi jika perlu)
            // Tipe text agar bisa menampung catatan yang panjang, nullable karena tidak wajib diisi
            $table->text('catatan_admin')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn('catatan_admin');
        });
    }
};