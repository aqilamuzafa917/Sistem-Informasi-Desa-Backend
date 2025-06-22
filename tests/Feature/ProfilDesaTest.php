<?php

test('get profil desa returns success', function () {
    $response = $this->getJson('/api/publik/profil-desa');
    $response->assertStatus(200);
}); 