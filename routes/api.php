<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\ProfilDesaController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ApbDesaController; // Tambahkan ini
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\DesaConfigController;

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
Route::get('/publik/profil-desa', [ProfilDesaController::class, 'index']); // Mengambil semua data profil desa
Route::get('/publik/profil-desa/{nama_desa}', [ProfilDesaController::class, 'showByName']); // Mengambil data profil desa berdasarkan nama

// Rute GET Surat berdasarkan NIK (Publik)
Route::get('/publik/surat/{nik}', [SuratController::class, 'showByNik']); // Lihat daftar surat berdasarkan NIK pengguna
Route::get('/publik/surat/{nik}/{id}/pdf', [SuratController::class, 'generatePDF']); // Download PDF surat (jika diinginkan publik)
Route::post('/publik/surat', [SuratController::class, 'store']);  // Membuat surat baru

// Rute Artikel Publik (Tanpa Autentikasi)
Route::get('/publik/artikel', [ArtikelController::class, 'publicIndex']); // Mendapatkan semua artikel publik
Route::get('/publik/artikel/{id}', [ArtikelController::class, 'publicShow']); // Mendapatkan detail artikel publik
Route::post('/publik/artikel', [ArtikelController::class, 'publicStore']); // Membuat artikel warga

// Rute Chatbot Publik
Route::post('/publik/chatbot/send', [ChatbotController::class, 'sendMessage']); // Mengirim pesan ke chatbot

// Rute Publik APB Desa
Route::get('/publik/apbdesa', [ApbDesaController::class, 'getLaporanApbDesa']); // 1 Tahun detail
Route::get('/publik/apbdesa/multi-tahun', [ApbDesaController::class, 'getLaporanMultiTahun']); // Pendapatan Belanja, tahun ke tahun
Route::get('/publik/apbdesa/statistik', [ApbDesaController::class, 'getStatistikApbDesa']); // Ringkasan Tahun ke Tahun\

// Rute Publik Pengaduan
Route::post('/publik/pengaduan', [PengaduanController::class, 'store']); // Membuat pengaduan baru
// Route untuk generate PDF APB Desa
Route::get('/publik/apb-desa/pdf/{tahun?}', [ApbDesaController::class, 'generatePDF']);

Route::get('/publik/profil-desa/{nama_desa}', [ProfilDesaController::class, 'showByName']);
Route::get('/publik/profil-desa/{id}', [ProfilDesaController::class, 'show']); // Get by ID
Route::get('/publik/profil-desa/{id}/identitas', [ProfilDesaController::class, 'getNamaDesa']); // Get nama_desa by ID
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
    Route::post('/surat', [SuratController::class, 'store']);  // Membuat surat baru
    Route::get('/surat/stats', [SuratController::class, 'getStats']); // API Statistik Surat
    
    // Route spesifik harus ditempatkan SEBELUM route dengan parameter {id}
    Route::get('/surat/sampah', [SuratController::class, 'trash']); // Melihat daftar surat yang telah dihapus
    
    // Route dengan parameter {id}
    Route::get('/surat/{id}', [SuratController::class, 'show']); // Admin melihat detail satu surat by ID
    Route::put('/surat/{id}', [SuratController::class, 'update']); // -- Ini untuk Revisi/Update Data Surat --
    Route::patch('/surat/{id}/status', [SuratController::class, 'updateStatus']); // Approve/Reject surat
    Route::delete('/surat/{id}', [SuratController::class, 'softDelete']); // Soft delete surat
    Route::patch('/surat/{id}/restore', [SuratController::class, 'restore']); // Mengembalikan surat yang telah dihapus
    // Route::delete('/surat/{id}/delete', [SuratController::class, 'forceDelete']); // Hapus surat secara permanen

    // CRUD Profil Desa (Admin)
    Route::get('/profil-desa', [ProfilDesaController::class, 'index']);
    Route::post('/profil-desa', [ProfilDesaController::class, 'store']); // Admin menyimpan atau memperbarui profil desa
    Route::get('/profil-desa/{id}', [ProfilDesaController::class, 'show']); // Get by ID
    Route::get('/profil-desa/{id}/nama', [ProfilDesaController::class, 'getNamaDesa']); // Get nama_desa by ID
    Route::patch('/profil-desa/{id}', [ProfilDesaController::class, 'update']); // Update specific fields
    Route::delete('/profil-desa/{nama_desa}', [ProfilDesaController::class, 'destroyByName']); 

    // CRUD Penduduk (Admin)
    Route::get('/penduduk/stats', [PendudukController::class, 'getStatistikPenduduk']);
    Route::get('/penduduk', [PendudukController::class, 'index']); // Admin melihat daftar semua penduduk
    Route::get('/penduduk/cari', [PendudukController::class, 'searchByNik']); // Admin mencari penduduk berdasarkan NIK
    Route::post('/penduduk', [PendudukController::class, 'addPenduduk']); // Admin menambahkan penduduk baru
    Route::put('/penduduk/{nik}', [PendudukController::class, 'updatePenduduk']); // Admin memperbarui data penduduk
    Route::delete('/penduduk/{nik}', [PendudukController::class, 'deletePenduduk']); // Admin menghapus penduduk
    
    // Routes untuk API Artikel
    Route::get('/artikel', [ArtikelController::class, 'index']);
    Route::post('/artikel', [ArtikelController::class, 'store']);
    Route::get('/artikel/stats', [ArtikelController::class, 'getArtikelStats']); // API Statistik Artikel
    Route::get('/artikel/{id}', [ArtikelController::class, 'show']);
    Route::put('/artikel/{id}', [ArtikelController::class, 'update']);
    Route::patch('/artikel/{id}/status', [ArtikelController::class, 'updateStatus']);
    Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy']);

    // CRUD Chatbot Logs (Admin)
    Route::get('/chatbot-logs/stats', [ChatbotController::class, 'adminGetStats']); // Mendapatkan statistik penggunaan chatbot
    Route::get('/chatbot-logs', [ChatbotController::class, 'adminIndexLogs']); // Melihat semua log chatbot
    Route::get('/chatbot-logs/{id}', [ChatbotController::class, 'adminShowLog']); // Melihat detail satu log chatbot
    Route::delete('/chatbot-logs/{id}', [ChatbotController::class, 'adminDestroyLog']); // Menghapus log chatbot

    // Realisasi Pendapatan
    Route::get('/pendapatan', [ApbDesaController::class, 'indexPendapatan']);
    Route::post('/pendapatan', [ApbDesaController::class, 'storePendapatan']);
    Route::get('/pendapatan/{id}', [ApbDesaController::class, 'showPendapatan']);
    Route::put('/pendapatan/{id}', [ApbDesaController::class, 'updatePendapatan']);
    Route::delete('/pendapatan/{id}', [ApbDesaController::class, 'destroyPendapatan']);
    
    // Realisasi Belanja
    Route::get('/belanja', [ApbDesaController::class, 'indexBelanja']);
    Route::post('/belanja', [ApbDesaController::class, 'storeBelanja']);
    Route::get('/belanja/{id}', [ApbDesaController::class, 'showBelanja']);
    Route::put('/belanja/{id}', [ApbDesaController::class, 'updateBelanja']);
    Route::delete('/belanja/{id}', [ApbDesaController::class, 'destroyBelanja']);

    // Total APB Desa
    Route::get('/apbdesa', [ApbDesaController::class, 'indexTotalApb']);

    //Routes untuk Pengaduan
    Route::get('/pengaduan', [PengaduanController::class, 'index']); // Melihat daftar pengaduan
    Route::get('/pengaduan/{pengaduan}', [PengaduanController::class, 'show']); // Melihat detail pengaduan
    Route::patch('/pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus']); // Mengupdate status pengaduan
    Route::delete('/pengaduan/{pengaduan}', [PengaduanController::class, 'destroy']); // Menghapus pengaduan
    Route::get('/pengaduan/kategori', [PengaduanController::class, 'filterByKategori']); // Filter pengaduan berdasarkan kategori
    Route::get('/pengaduan/filter', [PengaduanController::class, 'filterByStatus']); // Filter pengaduan berdasarkan status

    // Routes untuk Statistik Pengaduan
    Route::get('/pengaduan/stats', [PengaduanController::class, 'getStatistikPengaduan']); // Mendapatkan statistik pengaduan

    // Routes untuk API Map (POI)
    Route::get(('map'), [MapController::class, 'getBoundary']); // Mendapatkan data peta
    Route::get('/map/poi', [MapController::class, 'getPOI']); // Mendapatkan data POI berdasarkan amenity

    // Desa Config Routes
    Route::get('/desa-config', [DesaConfigController::class, 'getConfig']);
    Route::put('/desa-config', [DesaConfigController::class, 'updateConfig']);
});
