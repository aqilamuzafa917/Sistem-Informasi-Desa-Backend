<?php

use App\Models\ProfilDesa;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('get map boundary returns success', function () {
    ProfilDesa::create([
        'nama_desa' => 'Batujajar Timur',
        'polygon_desa' => [[107.5, -6.9], [107.6, -6.9], [107.6, -6.8], [107.5, -6.8], [107.5, -6.9]],
    ]);
    $response = $this->getJson('/api/publik/map');
    $response->assertStatus(200);
}); 