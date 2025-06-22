<?php

test('store pengaduan returns validation error for empty data', function () {
    $response = $this->postJson('/api/publik/pengaduan', []);
    $response->assertStatus(422);
}); 