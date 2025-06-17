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
        Schema::create('revoke_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revoker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('revoked_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('revoked_at');
            $table->timestamps();

            // Index for faster cooldown checks
            $table->index(['revoker_id', 'revoked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revoke_logs');
    }
}; 