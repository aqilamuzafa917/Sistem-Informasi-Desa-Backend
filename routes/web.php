<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "Desa " . config('desa.nama_desa');
});

// Route::get('/test-config', function () {
//     dd(config('desa.kode'));
// });

require __DIR__.'/auth.php';
