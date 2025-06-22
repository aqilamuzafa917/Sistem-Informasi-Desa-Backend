<?php

test('get idm returns success', function () {
    $response = $this->getJson('/api/publik/idm');
    $response->assertStatus(200);
}); 