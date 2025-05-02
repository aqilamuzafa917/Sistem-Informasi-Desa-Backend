<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\ProfilDesaController; // <-- Pastikan controller ini diimpor

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rute Surat (Membutuhkan Autentikasi)
    Route::post('/surat', [SuratController::class, 'store']);  // Ajukan surat
    Route::get('/surat', [SuratController::class, 'index']);   // Lihat daftar surat
    Route::put('/surat/{id}', [SuratController::class, 'update']); // Approve/reject surat
    Route::get('/surat/pdf/{id}', [SuratController::class, 'generatePDF']); // Download PDF

    // Rute POST Profil Desa (Membutuhkan Autentikasi)
    Route::post('/profil', [ProfilDesaController::class, 'store']); // Menyimpan atau memperbarui profil desa
});

// Rute GET Profil Desa (Tidak Membutuhkan Autentikasi)
Route::get('/profil', [ProfilDesaController::class, 'index']); // Mengambil semua data profil desa
Route::get('/profil/{nama_desa}', [ProfilDesaController::class, 'showByName']); // Mengambil data profil desa berdasarkan nama (case-insensitive, _ bisa jadi spasi)