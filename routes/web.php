<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Sistem Informasi Desa'];
});

require __DIR__.'/auth.php';
