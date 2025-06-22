<?php

use App\Models\TotalApbDesa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('get apbdesa returns success', function () {
    $user = User::factory()->create(['id' => 1]);
    TotalApbDesa::create([
        'tahun_anggaran' => date('Y'),
        'total_pendapatan' => 1000000,
        'total_belanja' => 800000,
        'saldo_sisa' => 200000,
        'tanggal_pelaporan' => now(),
        'user_id' => $user->id,
    ]);
    $response = $this->getJson('/api/publik/apbdesa');
    $response->assertStatus(200);
}); 