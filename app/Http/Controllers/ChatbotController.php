<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatbotLog; // Pastikan ini sudah ada
use Illuminate\Support\Facades\Auth; // Pastikan ini sudah ada
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk statistik

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $userMessage = $request->input('message');
        
        // --- AWAL MODIFIKASI UNTUK FALLBACK API KEY ---
        $primaryApiKey = env('GEMINI_API_KEY');
        $backupApiKey = env('GEMINI_API_KEY_BACKUP');
        $apiKeysToTry = [];

        if ($primaryApiKey) {
            $apiKeysToTry[] = ['key' => $primaryApiKey, 'name' => 'Primary API Key'];
        }
        if ($backupApiKey) {
            $apiKeysToTry[] = ['key' => $backupApiKey, 'name' => 'Backup API Key'];
        }

        if (empty($apiKeysToTry)) {
            Log::error('Tidak ada GEMINI_API_KEY yang dikonfigurasi di .env');
            return response()->json(['error' => 'Konfigurasi API Key tidak ditemukan'], 500);
        }
        // --- AKHIR MODIFIKASI UNTUK FALLBACK API KEY ---

        if (empty($userMessage)) {
            return response()->json(['error' => 'Pesan tidak boleh kosong'], 400);
        }

        // Mengambil informasi dari config/desa.php
        $configDesa = config('desa');
        $namaDesa = $configDesa['nama_desa'] ?? 'Desa Kami';
        $alamatDesa = $configDesa['alamat_desa'] ?? 'Alamat belum diatur';
        $emailDesa = $configDesa['email_desa'] ?? 'Email belum diatur';
        $teleponDesa = $configDesa['telepon_desa'] ?? 'Telepon belum diatur';
        $websiteDesa = $configDesa['website_desa'] ?? 'Website belum diatur';
        $namaKepalaDesa = $configDesa['nama_kepala_desa'] ?? 'Kepala Desa';
        $jabatanKepalaDesa = $configDesa['jabatan_kepala'] ?? 'Kepala Desa';
        $sosialMediaDesa = $configDesa['sosial_media'] ?? 'Sosial Media belum diatur';

        // Informasi layanan dan fitur di website (fokus pada frontend)
        // GANTI contoh path seperti '/peta-fasilitas', '/artikel', dll., dengan path frontend Anda yang sebenarnya.
        $infoLayanan = "Di website Sistem Informasi Desa {$namaDesa} ({$websiteDesa}), Anda dapat menemukan berbagai informasi dan layanan, termasuk:\n" .
                       "1.  **Peta Fasilitas Desa**: Untuk melihat lokasi sekolah, tempat ibadah, fasilitas kesehatan, dan fasilitas lainnya, serta peta alamat kantor desa. Anda bisa mencarinya di halaman 'Peta Fasilitas Desa' (contoh: {$websiteDesa}/peta-fasilitas).\n" .
                       "2.  **Artikel**: Membaca berita dan pengumuman terbaru dari desa, serta fitur bagi warga untuk mengirimkan artikel. Cari halaman 'Artikel', 'Berita', atau 'Pengumuman' (contoh: {$websiteDesa}/artikel untuk membaca, dan mungkin {$websiteDesa}/artikel/kirim untuk membuat artikel).\n" .
                       "3.  **Profil dan Identitas Desa**: Informasi lengkap mengenai profil desa, sejarah, visi & misi, serta struktur organisasi pemerintahan desa. Biasanya terdapat di halaman 'Profil Desa', 'Tentang Kami', atau sejenisnya (contoh: {$websiteDesa}/profil-desa).\n" .
                       "4.  **Infografis Data Kependudukan**: Data statistik penduduk seperti jumlah penduduk, KK, jenis kelamin, usia, agama, status, dan pekerjaan. Cari bagian 'Data Desa', 'Infografis', atau 'Statistik Penduduk' (contoh: {$websiteDesa}/data/kependudukan).\n" .
                       "5.  **Infografis Indeks Pembangunan Desa**: Informasi mengenai IDM (Indeks Desa Membangun) beserta skor dan statusnya, serta skor IKE, IKL, dan IKS. Ini mungkin ada di bagian 'Data Desa' atau 'Pembangunan Desa' (contoh: {$websiteDesa}/data/pembangunan).\n" .
                       "6.  **Infografis APB Desa**: Rangkuman Anggaran Pendapatan dan Belanja Desa, termasuk total pendapatan dan belanja. Cari di bagian 'Transparansi Anggaran' atau 'APBDes' (contoh: {$websiteDesa}/apbdes).\n" .
                       "7.  **Layanan Utama**: " .
                       "    - **Pengajuan Surat Keterangan (SK)**: Fitur untuk mengajukan berbagai surat keterangan secara online. Cari halaman 'Pengajuan SK' (contoh: {$websiteDesa}/layanan/pengajuan-sk). Jenis surat yang dapat dilayani meliputi SK Domisili
SK Kematian
SK Pindah
SK Kelahiran
SK Usaha
SK Kehilangan KTP
SK Kehilangan KK
Rekomendasi Kartu Indonesia Pintar
SK Tidak Mampu\n" .
                       "    - **Pantau Status SK**: Untuk melacak status pengajuan surat keterangan Anda. terintegrasi dalam sistem layanan surat membutuhkan NIK (contoh: {$websiteDesa}/layanan/cek-status-sk).\n" .
                       "    - **Pengaduan Warga**: Fitur untuk menyampaikan pengaduan atau aspirasi. Klik tombol merah ❗ di sebelah kiri chatbot untuk membuka form pengaduan.\n" .
                       "Anda juga dapat menanyakan prosedur administrasi umum di desa.";

        $systemInstruction = "### PERAN UTAMA & PERSONA ###
Anda adalah \"Asisten Desa Digital\" untuk website Sistem Informasi Desa {$namaDesa}. Persona Anda adalah Cerdas, Ramah, Proaktif, dan sangat Membantu. Tujuan utama Anda adalah mempermudah warga mendapatkan informasi dan menggunakan layanan desa secara online dengan memberikan jawaban yang akurat, jelas, dan actionable.

### KONTEKS UTAMA DESA ###
- **Nama Desa:** {$namaDesa}
- **Kepala Pemerintahan:** {$jabatanKepalaDesa} bernama {$namaKepalaDesa}.
- **Alamat Kantor Desa:** {$alamatDesa}
- **Website Resmi:** {$websiteDesa}
- **Kontak Email:** {$emailDesa}
- **Kontak Telepon:** {$teleponDesa}
- **Sosial Media:** {$sosialMediaDesa}

### BASIS PENGETAHUAN: LAYANAN & FITUR WEBSITE ###
Berikut adalah peta lengkap informasi dan layanan yang tersedia di website kami. Gunakan ini sebagai satu-satunya sumber kebenaran Anda.

**1. Profil & Informasi Umum Desa 📋**
   - **Fungsi:** Menyajikan informasi mendasar tentang desa.
   - **Konten:** Sejarah, Visi & Misi, Struktur Organisasi Pemerintahan Desa.
   - **URL Halaman:** {$websiteDesa}/profildesa
   - **Kata Kunci & Pertanyaan Umum:** \"tentang desa\", \"sejarah desa\", \"siapa saja perangkat desa\", \"visi misi\", \"struktur pemdes\".

**2. Pengajuan Surat Online ✉️**
   - **Fungsi:** Memungkinkan warga mengajukan permohonan surat keterangan secara digital.
   - **URL Halaman:** {$websiteDesa}/pengajuansurat
   - **Jenis Surat yang Dilayani:**
     - Surat Keterangan (SK) Domisili
     - SK Kematian
     - SK Pindah
     - SK Kelahiran
     - SK Usaha
     - SK Kehilangan KTP
     - SK Kehilangan KK
     - Rekomendasi Kartu Indonesia Pintar (KIP)
     - SK Tidak Mampu (SKTM)
   - **Kata Kunci & Pertanyaan Umum:** \"bikin surat\", \"urus SK\", \"prosedur surat domisili\", \"syarat sk usaha\", \"cara buat surat online\", \"minta surat keterangan\".

**3. Cek Status Pengajuan Surat 📊**
   - **Fungsi:** Melacak progres permohonan surat yang telah diajukan. Membutuhkan NIK pemohon.
   - **URL Halaman:** {$websiteDesa}/cekstatussurat
   - **Kata Kunci & Pertanyaan Umum:** \"cek status surat saya\", \"lacak pengajuan\", \"surat saya sudah jadi belum?\", \"pantau SK\".

**4. Artikel & Berita Desa 📰**
   - **Fungsi:** Publikasi berita, pengumuman, dan artikel informatif dari pemerintah desa atau kiriman warga.
   - **URL Halaman:** {$websiteDesa}/artikeldesa
   - **Kata Kunci & Pertanyaan Umum:** \"berita terbaru\", \"pengumuman desa\", \"info kegiatan\", \"baca artikel\", \"kirim tulisan\".

**5. Pengaduan Warga 🗣️**
   - **Fungsi:** Kanal resmi bagi warga untuk menyampaikan aspirasi, keluhan, atau laporan.
   - **Cara Akses:** Klik tombol ❗ di sebelah kiri chatbot untuk membuka form pengaduan.
   - **Kata Kunci & Pertanyaan Umum:** \"lapor masalah\", \"cara mengadu\", \"sampaikan keluhan\", \"aspirasi warga\", \"tombol pengaduan\", \"tombol merah\", \"tanda seru merah\".

**6. Peta Fasilitas Desa 🗺️**
   - **Fungsi:** Peta digital interaktif yang menunjukkan lokasi fasilitas penting di desa.
   - **Konten:** Lokasi sekolah, tempat ibadah, puskesmas/posyandu, kantor desa, dll.
   - **URL Halaman:** {$websiteDesa}/petafasilitasdesa
   - **Kata Kunci & Pertanyaan Umum:** \"lokasi sekolah\", \"alamat kantor desa dimana\", \"peta desa\", \"fasilitas umum\".

**7. Infografis Data Desa 📈**
   - **a. Data Kependudukan 👥**
     - **Konten:** Statistik visual jumlah penduduk, KK, jenis kelamin, rentang usia, agama, pekerjaan.
     - **URL Halaman:** {$websiteDesa}/infografis/penduduk
     - **Kata Kunci:** \"jumlah penduduk\", \"data demografi\", \"statistik warga\", \"berapa banyak laki-laki perempuan\".
   - **b. Anggaran Desa (APBDes) 💰**
     - **Konten:** Ringkasan visual Anggaran Pendapatan dan Belanja Desa (total pendapatan, belanja, sumber dana).
     - **URL Halaman:** {$websiteDesa}/infografis/apbdesa
     - **Kata Kunci:** \"dana desa\", \"anggaran desa\", \"transparansi apbdes\", \"pendapatan desa\".
   - **c. Indeks Desa Membangun (IDM) 📊**
     - **Konten:** Skor dan status IDM, serta skor komponen IKE, IKL, dan IKS.
     - **URL Halaman:** {$websiteDesa}/infografis/idm
     - **Kata Kunci:** \"status kemajuan desa\", \"IDM desa\", \"skor pembangunan\", \"indeks desa membangun\".

### PRINSIP UTAMA & ATURAN RESPON ###

1. **Pahami Maksud, Bukan Hanya Kata:** Fokus pada apa yang *sebenarnya* diinginkan pengguna. Gunakan bagian \"Kata Kunci & Pertanyaan Umum\" di atas untuk membantu mengidentifikasi maksud mereka, bahkan jika bahasanya tidak baku.

2. **Jadilah Proaktif dan Informatif:** Jangan hanya memberikan tautan. Berikan jawaban ringkas terlebih dahulu, lalu arahkan ke halaman yang relevan untuk detail lebih lanjut.
   - **Contoh Buruk:** \"Untuk mengajukan surat, klik di sini.\"
   - **Contoh Baik:** \"Tentu, Anda dapat mengajukan berbagai surat keterangan seperti SK Domisili dan SK Usaha secara online ✉️. Silakan kunjungi [Halaman Pengajuan Surat]({$websiteDesa}/pengajuansurat) untuk memulai prosesnya.\"

   - **Contoh Pertanyaan:** \"Apa saja fitur yang ada di website desa?\"
   - **Respons Cerdas:** \"Website Sistem Informasi Desa {$namaDesa} menyediakan berbagai fitur untuk memudahkan warga mengakses informasi dan layanan desa. Berikut adalah fitur-fitur yang tersedia:\n\n" .
     "1. **Profil & Informasi Umum Desa** 📋\n" .
     "   - Informasi lengkap tentang sejarah, visi & misi, dan struktur organisasi desa\n" .
     "   - Silakan kunjungi [Halaman Profil Desa]({$websiteDesa}/profildesa)\n\n" .
     "2. **Pengajuan Surat Online** ✉️\n" .
     "   - Layanan pengajuan berbagai jenis surat keterangan secara digital\n" .
     "   - Jenis surat: SK Domisili, SK Kematian, SK Pindah, SK Kelahiran, SK Usaha, SK Kehilangan KTP/KK, Rekomendasi KIP, SKTM\n" .
     "   - Silakan kunjungi [Halaman Pengajuan Surat]({$websiteDesa}/pengajuansurat)\n\n" .
     "3. **Cek Status Pengajuan Surat** 📊\n" .
     "   - Pantau status pengajuan surat Anda secara real-time\n" .
     "   - Membutuhkan NIK pemohon untuk pengecekan\n" .
     "   - Silakan kunjungi [Halaman Cek Status Surat]({$websiteDesa}/cekstatussurat)\n\n" .
     "4. **Artikel & Berita Desa** 📰\n" .
     "   - Baca berita dan pengumuman terbaru dari desa\n" .
     "   - Fitur untuk warga mengirimkan artikel\n" .
     "   - Silakan kunjungi [Halaman Artikel Desa]({$websiteDesa}/artikeldesa)\n\n" .
     "5. **Pengaduan Warga** 🗣️\n" .
     "   - Kanal resmi untuk menyampaikan aspirasi dan keluhan\n" .
     "   - Klik tombol merah❗ di sebelah kiri chatbot untuk membuka form pengaduan\n\n" .
     "6. **Peta Fasilitas Desa** 🗺️\n" .
     "   - Peta interaktif lokasi fasilitas penting di desa\n" .
     "   - Menampilkan lokasi sekolah, tempat ibadah, puskesmas, dan fasilitas lainnya\n" .
     "   - Silakan kunjungi [Halaman Peta Fasilitas]({$websiteDesa}/petafasilitasdesa)\n\n" .
     "7. **Infografis Data Desa** 📈\n" .
     "   - Data visual yang informatif tentang desa:\n" .
     "     • [Data Kependudukan]({$websiteDesa}/infografis/penduduk) 👥 - Statistik penduduk, KK, dan demografi\n" .
     "     • [APB Desa]({$websiteDesa}/infografis/apbdesa) 💰 - Anggaran dan penggunaan dana desa\n" .
     "     • [Indeks Desa Membangun]({$websiteDesa}/infografis/idm) 📊 - Skor dan status pembangunan desa\n\n" .
     "Silakan pilih fitur yang Anda butuhkan untuk informasi lebih lanjut.\"

   - **Contoh Pertanyaan:** \"Bagaimana kondisi desa kita?\"
   - **Respons Cerdas:** \"Untuk melihat kondisi desa secara menyeluruh, kami menyediakan tiga jenis infografis yang dapat Anda akses:\n" .
     "1. [Data Kependudukan]({$websiteDesa}/infografis/penduduk) 👥 - Menampilkan statistik penduduk, KK, dan demografi\n" .
     "2. [APB Desa]({$websiteDesa}/infografis/apbdesa) 💰 - Menunjukkan anggaran dan penggunaan dana desa\n" .
     "3. [Indeks Desa Membangun]({$websiteDesa}/infografis/idm) 📊 - Menampilkan skor dan status pembangunan desa\n\n" .
     "Silakan kunjungi halaman-halaman tersebut untuk informasi lebih detail.\"

   - **Contoh Pertanyaan:** \"Mau lapor masalah di desa\"
   - **Respons Cerdas:** \"Untuk menyampaikan pengaduan atau aspirasi, Anda dapat menggunakan fitur Pengaduan Warga 🗣️. Silakan klik tombol merah❗ yang berada di sebelah kiri chatbot ini untuk membuka form pengaduan. Tim kami akan segera menindaklanjuti pengaduan Anda.\"

3. **Eskalasi Cerdas (Upaya Terakhir):** Jika pertanyaan benar-benar di luar cakupan Anda atau memerlukan data pribadi yang sangat spesifik (misal: \"Berapa sisa tanah warisan kakek saya?\"), lakukan ini:
   - Akui keterbatasan Anda dengan sopan.
   - Sarankan solusi terbaik berikutnya, yaitu menghubungi pihak desa secara langsung.
   - **Contoh Respons:** \"Mohon maaf, saya tidak memiliki akses ke data spesifik mengenai detail rincian belanja APBDes per item. Informasi tersebut memerlukan verifikasi lebih lanjut. Untuk mendapatkan detailnya, saya sarankan Anda untuk datang langsung ke kantor desa di {$alamatDesa} atau menghubungi kami via telepon di {$teleponDesa} 📞. Petugas kami akan siap membantu Anda. 🙏\"

### PENANGANAN SPESIFIK: Sapaan Awal ###
- **Kondisi:** Aturan ini hanya berlaku untuk pesan **pertama** dari pengguna dalam sebuah percakapan.
- **Jika Pesan HANYA Sapaan:** Jika pesan pengguna hanya berisi sapaan singkat (seperti 'Halo', 'Hai', 'Permisi', 'P') dan **TIDAK** mengandung pertanyaan spesifik lainnya, jawablah dengan: \"Halo! 👋 Ada yang bisa saya bantu? Silakan ajukan pertanyaan Anda terkait Desa {$namaDesa}. Saya siap membantu memberikan informasi yang Anda butuhkan. 😊\"
- **Jika Pesan Sudah Mengandung Pertanyaan:** Jika pesan sapaan tersebut diikuti dengan pertanyaan (contoh: \"Pagi, mau tanya info APBDes\"), **ABAIKAN** sapaan generik di atas dan **LANGSUNG** jawab pertanyaan spesifik pengguna sesuai dengan Basis Pengetahuan dan Prinsip Respon yang telah ditetapkan.";

        // Gabungkan instruksi dengan pesan pengguna
        $fullPrompt = $systemInstruction . "\n\nPertanyaan Pengguna: " . $userMessage;

     
        $chatbotReply = null;
        $lastErrorResponse = null;
        $successfulKeyName = null;

        foreach ($apiKeysToTry as $apiKeyData) {
            $currentApiKey = $apiKeyData['key'];
            $currentApiKeyName = $apiKeyData['name'];
            $geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$currentApiKey}";

            try {
                Log::info("Mencoba menghubungi Gemini API menggunakan {$currentApiKeyName}.");
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($geminiApiUrl, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $fullPrompt
                                ]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $candidates = $response->json()['candidates'] ?? null;
                    if ($candidates && isset($candidates[0]['content']['parts'][0]['text'])) {
                        $chatbotReply = $candidates[0]['content']['parts'][0]['text'];
                        $successfulKeyName = $currentApiKeyName;
                        Log::info("Berhasil mendapatkan balasan dari Gemini API menggunakan {$successfulKeyName}.");
                        break; // Keluar dari loop jika berhasil
                    } else {
                        Log::error("Struktur respons Gemini tidak sesuai harapan menggunakan {$currentApiKeyName}: " . $response->body());
                        $lastErrorResponse = response()->json(['error' => 'Gagal memproses respons dari AI Chatbot'], 500);
                        // Tidak break, coba key berikutnya jika ada
                    }
                } else {
                    Log::error("Error dari Gemini API menggunakan {$currentApiKeyName}: " . $response->status() . ' - ' . $response->body());
                    $lastErrorResponse = response()->json(['error' => 'Gagal menghubungi AI Chatbot', 'details' => $response->json()], $response->status());
                    
                    $responseData = $response->json();
                    $isApiKeyInvalidError = false;
                    if ($response->status() == 400 && 
                        isset($responseData['error']['details'][0]['reason']) && 
                        $responseData['error']['details'][0]['reason'] === 'API_KEY_INVALID') {
                        $isApiKeyInvalidError = true;
                    }

                    // Jika error 401, 403, atau 400 dengan API_KEY_INVALID, kemungkinan key tidak valid, coba key berikutnya
                    if (in_array($response->status(), [401, 403]) || $isApiKeyInvalidError) {
                        Log::info("API Key {$currentApiKeyName} tidak valid atau bermasalah (Status: {$response->status()}), mencoba key berikutnya.");
                        continue; // Coba key berikutnya
                    }
                    // Untuk error lain, mungkin bukan masalah key, jadi break dan laporkan error terakhir
                    Log::warning("Error lain dari Gemini API menggunakan {$currentApiKeyName} (Status: {$response->status()}), menghentikan percobaan key.");
                    break; 
                }
            } catch (\Exception $e) {
                Log::error("Exception saat menghubungi Gemini API menggunakan {$currentApiKeyName}: " . $e->getMessage());
                $lastErrorResponse = response()->json(['error' => 'Terjadi kesalahan pada server saat menghubungi AI Chatbot'], 500);
                // Jika exception, coba key berikutnya jika ada
                continue;
            }
        }
      
        if ($chatbotReply !== null) {
            ChatbotLog::create([
                'user_message' => $userMessage,
                'chatbot_reply' => $chatbotReply,
                'ip_address' => $request->ip(),
                'user_id' => Auth::check() ? Auth::id() : null,
               
            ]);
            return response()->json(['reply' => $chatbotReply]);
        } else {
            // Jika semua key gagal atau terjadi error yang menghentikan loop
            $errorMessage = 'Error: Semua API Key gagal.';
            if ($lastErrorResponse) {
                $errorData = $lastErrorResponse->getData();
                $errorMessage = 'Error: ' . ($errorData->error ?? 'Tidak diketahui');
                if (isset($errorData->details->error->message)) { // Check nested structure from Gemini
                    $errorMessage .= ' - ' . $errorData->details->error->message;
                } elseif (isset($errorData->details[0]->message)) { // Check if details is an array
                     $errorMessage .= ' - ' . $errorData->details[0]->message;
                }
            }

            ChatbotLog::create([
                'user_message' => $userMessage,
                'chatbot_reply' => $errorMessage,
                'ip_address' => $request->ip(),
                'user_id' => Auth::check() ? Auth::id() : null,
            ]);
            return $lastErrorResponse ?: response()->json(['error' => 'Semua upaya koneksi ke AI Chatbot gagal.'], 500);
        }
    }

    // --- AWAL CRUD UNTUK CHATBOT LOGS (ADMIN) ---

    /**
     * Menampilkan daftar semua log chatbot untuk admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminIndexLogs(Request $request)
    {
        // Ambil parameter paginasi dari request, default 15 item per halaman
        $perPage = $request->input('per_page', 15);
        $logs = ChatbotLog::orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($logs);
    }

    /**
     * Menampilkan detail satu log chatbot untuk admin.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminShowLog($id)
    {
        $log = ChatbotLog::find($id);

        if (!$log) {
            return response()->json(['message' => 'Log chatbot tidak ditemukan'], 404);
        }

        return response()->json($log);
    }

    /**
     * Menghapus satu log chatbot untuk admin.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminDestroyLog($id)
    {
        $log = ChatbotLog::find($id);

        if (!$log) {
            return response()->json(['message' => 'Log chatbot tidak ditemukan'], 404);
        }

        $log->delete();

        return response()->json(['message' => 'Log chatbot berhasil dihapus']);
    }

    /**
     * Mendapatkan statistik penggunaan chatbot.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminGetStats()
    {
        $totalMessages = ChatbotLog::count();
        $todayMessages = ChatbotLog::whereDate('created_at', today())->count();
        $thisWeekMessages = ChatbotLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthMessages = ChatbotLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        // Contoh statistik tambahan: Pesan per hari selama 7 hari terakhir
        $dailyMessagesLast7Days = ChatbotLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay()) // 6 hari lalu + hari ini = 7 hari
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Contoh statistik tambahan: Top 5 IP Address yang paling sering berinteraksi
        $topIpAddresses = ChatbotLog::select('ip_address', DB::raw('count(*) as total'))
            ->groupBy('ip_address')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'total_messages_all_time' => $totalMessages,
            'total_messages_today' => $todayMessages,
            'total_messages_this_week' => $thisWeekMessages,
            'total_messages_this_month' => $thisMonthMessages,
            'daily_messages_last_7_days' => $dailyMessagesLast7Days,
            'top_5_ip_addresses' => $topIpAddresses,
        ]);
    }

    // --- AKHIR CRUD UNTUK CHATBOT LOGS (ADMIN) ---
}