<?php

test('register endpoint returns a response', function () {
    $response = $this->postJson('/api/register', [
        // 'name' => 'Test', 'email' => 'test@example.com', 'password' => 'password',
    ]);
    $response->assertStatus(422); // Expect validation error for empty data
});

test('login endpoint returns a response', function () {
    $response = $this->postJson('/api/login', [
        // 'email' => 'test@example.com', 'password' => 'password',
    ]);
    $response->assertStatus(422); // Expect validation error for empty data
}); 