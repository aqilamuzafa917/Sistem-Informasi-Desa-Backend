<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\ProfilDesaController;
use App\Http\Controllers\PendudukController;

/*
|--------------------------------------------------------------------------
| Rute Autentikasi Admin
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']); // Mungkin hanya untuk setup awal admin
Route::post('/login', [AuthController::class, 'login']); // Admin login

/*
|--------------------------------------------------------------------------
| Rute Publik (Tidak Membutuhkan Autentikasi)
|--------------------------------------------------------------------------
*/
// Rute GET Profil Desa
Route::get('/profil', [ProfilDesaController::class, 'index']); // Mengambil semua data profil desa
Route::get('/profil/{nama_desa}', [ProfilDesaController::class, 'showByName']); // Mengambil data profil desa berdasarkan nama

// Rute GET Surat berdasarkan NIK (Publik)
Route::get('/surat/nik/{nik}', [SuratController::class, 'showByNik']); // Lihat daftar surat berdasarkan NIK pengguna
Route::get('/surat/pdf/{id}', [SuratController::class, 'generatePDF']); // Download PDF surat (jika diinginkan publik)
// Catatan: Pertimbangkan apakah download PDF harus publik atau memerlukan NIK/auth.
Route::post('/surat', [SuratController::class, 'store']);  // Membuat surat baru


/*
|--------------------------------------------------------------------------
| Rute Admin (Membutuhkan Autentikasi - Sanctum) - CRUD Lengkap
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth Admin
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) { // Mendapatkan info admin yang login
        return $request->user();
    });

    // CRUD Surat (Admin)
    
    Route::get('/surat', [SuratController::class, 'index']);   // Admin melihat daftar semua surat
    Route::put('/surat/{id}/status', [SuratController::class, 'updateStatus']); //Approve/Reject surat
    //Route::delete('/surat/{id}', [SuratController::class, 'destroy']); // Menghapus surat
    Route::get('/surat/{id}', [SuratController::class, 'show']); // Admin melihat detail satu surat by ID
    // Route::delete('/surat/{id}', [SuratController::class, 'destroy']);
    Route::put('/surat/{id}', [SuratController::class, 'update']); // -- Ini untuk Revisi/Update Data Surat --


    // CRUD Profil Desa (Admin)
    Route::post('/profil', [ProfilDesaController::class, 'store']); // Admin menyimpan atau memperbarui profil desa
    Route::delete('/profil/{nama_desa}', [ProfilDesaController::class, 'destroyByName']); 

    // CRUD Penduduk (Admin)
    Route::get('/penduduk', [PendudukController::class, 'index']); // Admin melihat daftar semua penduduk
    Route::get('/penduduk/search', [PendudukController::class, 'searchByNik']); // Admin mencari penduduk berdasarkan NIK
});