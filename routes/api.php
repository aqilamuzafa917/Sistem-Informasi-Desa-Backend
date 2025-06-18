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
use App\Http\Controllers\IDMController;

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

// Tambah route cek nik dan tanggal lahir
Route::post('/publik/cek-nik-tanggal-lahir', [PendudukController::class, 'checkNikTanggalLahir']);

// Rute GET Surat berdasarkan NIK (Publik)
Route::get('/publik/surat/{nik}', [SuratController::class, 'showByNik']); // Lihat daftar surat berdasarkan NIK pengguna
Route::get('/publik/surat/{nik}/{id}/pdf', [SuratController::class, 'generatePDF']); // Download PDF surat (jika diinginkan publik)
Route::post('/publik/surat', [SuratController::class, 'store']);  // Membuat surat baru
Route::get('/publik/surat-latest/{nik}', [SuratController::class, 'latestShowByNik']); // Lihat 3 surat terbaru berdasarkan NIK pengguna

// Rute Artikel Publik (Tanpa Autentikasi)
Route::get('/publik/artikel-latest', [ArtikelController::class, 'latestpublicIndex']); // Mendapatkan semua artikel publik
Route::get('/publik/artikel', [ArtikelController::class, 'publicIndex']); // Mendapatkan semua artikel publik
Route::get('/publik/artikel/{id}', [ArtikelController::class, 'publicShow']); // Mendapatkan detail artikel publik
Route::post('/publik/artikel', [ArtikelController::class, 'publicStore']); // Membuat artikel warga

// Rute Chatbot Publik
Route::post('/publik/chatbot/send', [ChatbotController::class, 'sendMessage']); // Mengirim pesan ke chatbot

// Rute Publik APB Desa
Route::get('/publik/apbdesa', [ApbDesaController::class, 'getLaporanApbDesa']); // 1 Tahun detail
Route::get('/publik/apbdesa/multi-tahun', [ApbDesaController::class, 'getLaporanMultiTahun']); // Pendapatan Belanja, tahun ke tahun
Route::get('/publik/apbdesa/statistik', [ApbDesaController::class, 'getStatistikApbDesa']); // Ringkasan Tahun ke Tahun\
Route::get('/publik/apbdesa-chatbot', [ApbDesaController::class, 'getLaporanApbDesaForChatbot']); // Ringkasan Tahun ke Tahun\
// Rute Publik Pengaduan
Route::post('/publik/pengaduan', [PengaduanController::class, 'store']); // Membuat pengaduan baru
// Route untuk generate PDF APB Desa
Route::get('/publik/apb-desa/pdf/{tahun?}', [ApbDesaController::class, 'generatePDF']);

// Route::get('/publik/profil-desa/{nama_desa}', [ProfilDesaController::class, 'showByName']);
Route::get('/publik/profil-desa/{id}', [ProfilDesaController::class, 'show']); // Get by ID
Route::get('/publik/profil-desa/{id}/identitas', [ProfilDesaController::class, 'getNamaDesa']); // Get nama_desa by ID


// Routes untuk API Map (POI)
Route::get('/publik/map', [MapController::class, 'getBoundary']); // Mendapatkan data peta
Route::get('/publik/map/poi', [MapController::class, 'getPOI']); // Mendapatkan data POI berdasarkan amenity
Route::get('/publik/map/poi/{kategori}', [MapController::class, 'showByKategori']); // Mendapatkan data POI berdasarkan kategori
Route::get('/publik/desa-config', [DesaConfigController::class, 'getConfig']);

Route::get('/publik/penduduk/stats', [PendudukController::class, 'getStatistikPenduduk']);
Route::get('/publik/penduduk/stats-chatbot', [PendudukController::class, 'getStatistikPendudukForChatbot']);
Route::get('/publik/penduduk/{nik}', [PendudukController::class, 'getNamaByNik']);


// Rute untuk IDM
Route::get('/publik/idm/{tahun}', [App\Http\Controllers\IDMController::class, 'show']); // Mendapatkan data IDM


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
    Route::get('/user-list', [AuthController::class, 'getAllUsers']); // Get all users with pagination
     // User management routes
    Route::post('/users/{id}/revoke', [AuthController::class, 'revokeUser']);
    Route::post('/users/{id}/reactivate', [AuthController::class, 'reactivateUser']);
    Route::get('/users', [AuthController::class, 'getAllUsers']);

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
    Route::get('/profil-desa/{id}/identitas', [ProfilDesaController::class, 'getNamaDesa']); // Get nama_desa by ID
    Route::patch('/profil-desa/{id}', [ProfilDesaController::class, 'update']); // Update specific fields
    //Route::patch('/profil-desa/{id}/struktur-organisasi', [ProfilDesaController::class, 'updateStrukturOrganisasi']); // Update struktur_organisasi only
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

    // Routes untuk Statistik Pengaduan
    Route::get('/pengaduan/stats', [PengaduanController::class, 'getStatistikPengaduan']); // Mendapatkan statistik pengaduan

    //Routes untuk Pengaduan
    Route::get('/pengaduan', [PengaduanController::class, 'index']); // Melihat daftar pengaduan
    Route::get('/pengaduan/kategori', [PengaduanController::class, 'filterByKategori']); // Filter pengaduan berdasarkan kategori
    Route::get('/pengaduan/filter', [PengaduanController::class, 'filterByStatus']); // Filter pengaduan berdasarkan status
    Route::get('/pengaduan/{pengaduan}', [PengaduanController::class, 'show']); // Melihat detail pengaduan
    Route::patch('/pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus']); // Mengupdate status pengaduan
    Route::delete('/pengaduan/{pengaduan}', [PengaduanController::class, 'destroy']); // Menghapus pengaduan

    // Desa Config Routes
    Route::get('/desa-config', [DesaConfigController::class, 'getConfig']);
    Route::put('/desa-config', [DesaConfigController::class, 'updateConfig']);

    // CRUD Potensi (Map)
    Route::post('/map/poi', [MapController::class, 'store']); // Menambahkan POI baru
    Route::put('/map/poi/{id}', [MapController::class, 'update']); // Memperbarui POI
    Route::delete('/map/poi/{id}', [MapController::class, 'destroy']); // Menghapus POI

    // Routes untuk Menambahkan Variabel IDM -> isi ini dulu sampe semua lengkap, baru hit IDM
    Route::get('/variabel-idm', [App\Http\Controllers\VariabelIDMController::class, 'create']); // Mendapatkan daftar indikator IDM
    Route::post('/variabel-idm', [App\Http\Controllers\VariabelIDMController::class, 'store']); // Menyimpan variabel IDM
    Route::get('/variabel-idm/{tahun}', [App\Http\Controllers\VariabelIDMController::class, 'show']); // Mendapatkan daftar tahun variabel IDM
    Route::get('/variabel-idm/edit/{variabelIDM}', [App\Http\Controllers\VariabelIDMController::class, 'edit']); // Mengedit variabel IDM
    Route::put('/variabel-idm/edit/{variabelIDM}', [App\Http\Controllers\VariabelIDMController::class, 'update']); // Memperbarui variabel IDM
    Route::delete('/variabel-idm/{variabelIDM}', [App\Http\Controllers\VariabelIDMController::class, 'destroy']); // Menghapus variabel IDM

    // Routes utama IDM
    Route::post('/idm/{tahun}/recalculate', [IDMController::class, 'recalculate']);
   
    // Routes untuk Indikator IDM
    Route::post('/indikator-idm', [App\Http\Controllers\IndikatorIDMController::class, 'store']); // Menyimpan indikator IDM
    Route::put('/indikator-idm/batch-update', [App\Http\Controllers\IndikatorIDMController::class, 'batchUpdate']); // Memperbarui beberapa indikator IDM sekaligus
    Route::put('/indikator-idm/{indikatorIDM}', [App\Http\Controllers\IndikatorIDMController::class, 'update']); // Memperbarui indikator IDM
    Route::delete('/indikator-idm/{indikatorIDM}', [App\Http\Controllers\IndikatorIDMController::class, 'destroy']); // Menghapus indikator IDM
});

