<?php

use App\Models\User;

test('user model basic instantiation', function () {
    $user = new User();
    expect($user)->toBeInstanceOf(User::class);
}); 