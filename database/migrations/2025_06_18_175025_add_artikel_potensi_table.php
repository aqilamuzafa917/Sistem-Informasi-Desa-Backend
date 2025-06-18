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
        Schema::table('artikels', function (Blueprint $table) {
            $table->unsignedBigInteger('potensi_id')->nullable()->after('media_artikel');

            // Add foreign key constraint if the potensi_loc table exists
            if (Schema::hasTable('potensi_loc')) {
                $table->foreign('potensi_id')
                      ->references('id')
                      ->on('potensi_loc')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
