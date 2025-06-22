<?php

test('get artikel latest returns success', function () {
    $response = $this->getJson('/api/publik/artikel-latest');
    $response->assertStatus(200);
}); 