<?php

test('send chatbot message returns validation error for empty data', function () {
    $response = $this->postJson('/api/publik/chatbot/send', []);
    $response->assertStatus(400);
}); 