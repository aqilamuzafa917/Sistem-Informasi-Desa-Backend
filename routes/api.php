<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/surat', [SuratController::class, 'store']);  // Ajukan surat
    Route::get('/surat', [SuratController::class, 'index']);   // Lihat daftar surat
    Route::put('/surat/{id}', [SuratController::class, 'update']); // Approve/reject surat
    Route::get('/surat/pdf/{id}', [SuratController::class, 'generatePDF']); // Download PDF
});
