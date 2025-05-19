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
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->text('user_message');
            $table->text('chatbot_reply');
            $table->ipAddress('ip_address')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Jika Anda memiliki tabel users dan ingin menghubungkannya
            $table->string('session_id')->nullable()->index(); // Untuk melacak sesi anonim jika diperlukan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }
};