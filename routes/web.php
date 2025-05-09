<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Sistem Informasi Desa'];
});

// Route::get('/test-config', function () {
//     dd(config('desa.kode'));
// });

require __DIR__.'/auth.php';
