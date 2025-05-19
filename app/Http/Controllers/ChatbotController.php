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
                       "    - **Pengaduan Warga**: Fitur untuk menyampaikan pengaduan atau aspirasi. Cari halaman 'Pengaduan Warga' (contoh: {$websiteDesa}/pengaduan).\n" .
                       "Anda juga dapat menanyakan prosedur administrasi umum di desa.";

        $systemInstruction = "Anda adalah asisten AI untuk website Sistem Informasi Desa {$namaDesa}. " .
                             "Alamat kantor desa kami adalah {$alamatDesa}. " .
                             "Anda dapat menghubungi kami melalui email: {$emailDesa} atau telepon: {$teleponDesa}. " .
                             "Website resmi kami adalah {$websiteDesa}. " .
                             "Anda juga bisa menemukan kami di sosial media: {$sosialMediaDesa}. ".
                             "Saat ini, desa kami dipimpin oleh seorang {$jabatanKepalaDesa} yang bernama {$namaKepalaDesa}. " .
                             "Tugas Anda adalah menjawab pertanyaan warga terkait layanan desa, informasi umum desa, dan prosedur administrasi berdasarkan informasi yang diberikan. " .
                             "Berikut adalah ringkasan fitur dan informasi yang ada di website kami:\n" . $infoLayanan . "\n\n" .
                             "Ketika menjawab pertanyaan tentang fitur, jelaskan bahwa fitur tersebut tersedia di website kami dan arahkan pengguna ke halaman yang relevan di website {$websiteDesa} menggunakan contoh path yang telah disebutkan. " .
                             "Contoh: Jika pengguna bertanya 'Di mana saya bisa melihat peta sekolah?', Anda bisa menjawab 'Anda dapat melihat peta fasilitas sekolah di website kami ({$websiteDesa}) pada halaman 'Fasilitas Desa'. Contohnya, Anda bisa coba kunjungi {$websiteDesa}/peta-fasilitas.'. " .
                             "Jika pengguna bertanya tentang layanan seperti 'Bagaimana cara mengajukan surat keterangan usaha?', jawab dengan 'Anda bisa mengajukan surat keterangan usaha secara online melalui website kami di {$websiteDesa} pada bagian layanan surat, biasanya di halaman 'Pengajuan SK'. Contohnya, Anda bisa coba kunjungi {$websiteDesa}/layanan/pengajuan-sk.'. " .
                             "Selalu gunakan bahasa yang sopan, ramah, dan mudah dimengerti serta yakin. Jangan menggunakan kata yang meragukan (biasanya, seperti, contohnya dll.) " .
                             "Jika Anda tidak tahu jawabannya atau pertanyaan bersifat sangat spesifik dan memerlukan data pribadi yang tidak Anda miliki, " .
                             "sarankan pengguna untuk mengunjungi kantor desa secara langsung atau melalui kontak yang tersedia.";

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