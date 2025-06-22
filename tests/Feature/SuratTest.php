<?php

test('get surat by NIK returns not found for unknown NIK', function () {
    $response = $this->getJson('/api/publik/surat/0000000000000000');
    $response->assertStatus(404);
});

test('store surat returns validation error for empty data', function () {
    $response = $this->postJson('/api/publik/surat', []);
    $response->assertStatus(422);
}); 