<?php

test('get penduduk stats returns success', function () {
    $response = $this->getJson('/api/publik/penduduk/stats');
    $response->assertStatus(200);
}); 