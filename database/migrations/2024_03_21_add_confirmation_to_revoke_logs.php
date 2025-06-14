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
        Schema::table('revoke_logs', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending')->after('revoked_at');
            $table->string('confirmation_code', 6)->nullable()->after('status');
            $table->timestamp('confirmation_expires_at')->nullable()->after('confirmation_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revoke_logs', function (Blueprint $table) {
            $table->dropColumn(['status', 'confirmation_code', 'confirmation_expires_at']);
        });
    }
}; 