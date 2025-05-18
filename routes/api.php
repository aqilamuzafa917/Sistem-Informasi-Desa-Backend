<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\ProfilDesaController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\ArtikelController;

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
Route::get('/publik/profil', [ProfilDesaController::class, 'index']); // Mengambil semua data profil desa
Route::get('/publik/profil/{nama_desa}', [ProfilDesaController::class, 'showByName']); // Mengambil data profil desa berdasarkan nama

// Rute GET Surat berdasarkan NIK (Publik)
Route::get('/publik/surat/nik/{nik}', [SuratController::class, 'showByNik']); // Lihat daftar surat berdasarkan NIK pengguna
Route::get('/publik/surat/pdf/{nik}/{id}', [SuratController::class, 'generatePDF']); // Download PDF surat (jika diinginkan publik)
Route::post('/publik/surat', [SuratController::class, 'store']);  // Membuat surat baru

// Rute Artikel Publik (Tanpa Autentikasi)
Route::get('/publik/artikel', [ArtikelController::class, 'publicIndex']); // Mendapatkan semua artikel publik
Route::get('/publik/artikel/{id}', [ArtikelController::class, 'publicShow']); // Mendapatkan detail artikel publik
Route::post('/publik/artikel', [ArtikelController::class, 'publicStore']); // Membuat artikel warga


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
    Route::post('/surat', [SuratController::class, 'store']);  // Membuat surat baru
    Route::get('/surat', [SuratController::class, 'index']);   // Admin melihat daftar semua surat
    Route::put('/surat/status/{id}', [SuratController::class, 'updateStatus']); //Approve/Reject surat
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
    Route::post('/penduduk/add', [PendudukController::class, 'addPenduduk']); // Admin menambahkan penduduk baru
    Route::put('/penduduk/{nik}', [PendudukController::class, 'updatePenduduk']); // Admin memperbarui data penduduk
    Route::delete('/penduduk/{nik}', [PendudukController::class, 'deletePenduduk']); // Admin menghapus penduduk

    // Routes untuk API Artikel
    Route::get('/artikel', [ArtikelController::class, 'index']);
    Route::post('/artikel', [ArtikelController::class, 'store']);
    Route::get('/artikel/{id}', [ArtikelController::class, 'show']);
    Route::put('/artikel/{id}', [ArtikelController::class, 'update']);
    Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy']);
    Route::put('/artikel/status/{id}', [ArtikelController::class, 'updateStatus']);
});