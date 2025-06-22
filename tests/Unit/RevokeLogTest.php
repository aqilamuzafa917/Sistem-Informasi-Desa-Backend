<?php

use App\Models\RevokeLog;

test('revoke log model basic instantiation', function () {
    $log = new RevokeLog();
    expect($log)->toBeInstanceOf(RevokeLog::class);
}); 