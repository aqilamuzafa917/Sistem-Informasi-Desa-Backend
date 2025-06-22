<?php

use App\Models\ChatbotLog;

test('chatbot log model basic instantiation', function () {
    $log = new ChatbotLog();
    expect($log)->toBeInstanceOf(ChatbotLog::class);
}); 