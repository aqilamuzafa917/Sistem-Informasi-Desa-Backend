<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatbotLog; // Pastikan ini sudah ada
use Illuminate\Support\Facades\Auth; // Pastikan ini sudah ada
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk statistik
use App\Http\Controllers\SuratController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\ApbDesaController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\IDMController; // [ADDED] Import IDMController
use Illuminate\Http\Client\Response; // Add this import

class ChatbotController extends Controller
{
    /**
     * Definisi fungsi yang tersedia untuk dipanggil oleh model AI.
     * Strukturnya mengikuti skema yang dibutuhkan oleh Gemini API.
     */
    private $availableFunctions = [
        [
            'name' => 'get_surat_by_nik',
            'description' => 'Mendapatkan 3 surat terbaru berdasarkan NIK (Nomor Induk Kependudukan) pemohon. Fungsi ini hanya menampilkan 3 pengajuan surat terakhir untuk memudahkan pengecekan status surat terkini.',
            'parameters' => [
                'type' => 'OBJECT',
                'properties' => [
                    'nik' => [
                        'type' => 'STRING',
                        'description' => 'NIK (Nomor Induk Kependudukan) 16 digit milik pemohon surat.'
                    ]
                ],
                'required' => ['nik']
            ]
        ],
        [
            'name' => 'get_artikel_list',
            'description' => 'Mendapatkan 3 artikel terbaru dari desa. Fungsi ini menampilkan artikel-artikel terbaru yang sudah disetujui untuk dibaca oleh warga.',
            'parameters' => [
                'type' => 'OBJECT',
                'properties' => [
                    'kategori' => [
                        'type' => 'STRING',
                        'description' => 'Kategori artikel (opsional). Jika tidak diisi, akan menampilkan semua kategori.'
                    ]
                ],
                'required' => []
            ]
        ],
        [
            'name' => 'get_artikel_by_id',
            'description' => 'Mendapatkan detail artikel tertentu berdasarkan ID artikel. Fungsi ini menampilkan informasi lengkap tentang satu artikel yang sudah disetujui.',
            'parameters' => [
                'type' => 'OBJECT',
                'properties' => [
                    'id' => [
                        'type' => 'STRING',
                        'description' => 'ID artikel yang ingin dilihat detailnya.'
                    ]
                ],
                'required' => ['id']
            ]
        ],
        [
            'name' => 'get_laporan_apbdesa',
            'description' => 'Mendapatkan laporan Anggaran Pendapatan dan Belanja Desa (APBDes) untuk tahun tertentu. Jika tahun tidak disebutkan, akan menampilkan data untuk tahun terbaru yang tersedia.',
            'parameters' => [
                'type' => 'OBJECT',
                'properties' => [
                    'tahun' => [
                        'type' => 'INTEGER',
                        'description' => 'Tahun anggaran 4 digit yang diminta oleh pengguna (contoh: 2023).'
                    ]
                ],
                'required' => ['tahun']
            ]
        ],
        [
            'name' => 'get_statistik_penduduk',
            'description' => 'Mendapatkan data statistik kependudukan desa secara komprehensif, mencakup jumlah total penduduk, KK, jenis kelamin, serta rincian berdasarkan rentang usia, agama, status perkawinan, tingkat pendidikan, dan jenis pekerjaan.',
            'parameters' => [
                'type' => 'OBJECT'
            ]
        ],
        // [ADDED] New function definition for IDM
        [
            'name' => 'get_idm_data',
            'description' => 'Mendapatkan data Indeks Desa Membangun (IDM) untuk tahun tertentu. IDM adalah pengukuran komposit untuk menggambarkan kemajuan dan kemandirian desa. Jika tahun tidak disebutkan, akan menampilkan data untuk tahun terbaru yang tersedia.',
            'parameters' => [
                'type' => 'OBJECT',
                'properties' => [
                    'tahun' => [
                        'type' => 'INTEGER',
                        'description' => 'Tahun data IDM 4 digit yang diminta (opsional, contoh: 2023). Jika tidak diisi, akan mengambil data tahun terbaru.'
                    ]
                ],
                'required' => []
            ]
        ]
    ];

    /**
     * Mengeksekusi fungsi lokal berdasarkan nama dan argumen dari model AI.
     */
    private function executeFunctionCall(string $functionName, array $args)
    {
        try {
            switch ($functionName) {
                case 'get_surat_by_nik':
                    $suratController = app(SuratController::class);
                    // Pastikan argumen 'nik' ada sebelum dipanggil
                    $nik = $args['nik'] ?? null;
                    if (!$nik) {
                        return response()->json(['error' => 'Argument "nik" is required for get_surat_by_nik.'], 400);
                    }
                    return $suratController->latestShowByNik($nik);
                
                case 'get_artikel_list':
                    $artikelController = app(ArtikelController::class);
                    $request = new Request();
                    if (isset($args['kategori'])) {
                        $request->merge(['kategori' => $args['kategori']]);
                    }
                    $response = $artikelController->latestPublicIndex($request);
                    
                    // Proses rangkuman artikel
                    if ($response->getStatusCode() === 200) {
                        $data = $response->getData(true);
                        if (isset($data['data']) && is_array($data['data'])) {
                            foreach ($data['data'] as &$artikel) {
                                // Bersihkan konten dari HTML tags dan ambil 150 karakter pertama
                                $isi = strip_tags($artikel['isi_artikel']);
                                $artikel['rangkuman'] = strlen($isi) > 150 ? substr($isi, 0, 150) . '...' : $isi;
                            }
                        }
                    }
                    return response()->json($data);
                
                case 'get_artikel_by_id':
                    $artikelController = app(ArtikelController::class);
                    $id = $args['id'] ?? null;
                    if (!$id) {
                        return response()->json(['error' => 'Argument "id" is required for get_artikel_by_id.'], 400);
                    }
                    return $artikelController->publicShow($id);

                case 'get_laporan_apbdesa':
                    $apbDesaController = app(ApbDesaController::class);
                    $request = new Request($args); // Langsung kirim argumen sebagai request
                    return $apbDesaController->getLaporanApbDesaForChatbot($request);
                
                case 'get_statistik_penduduk':
                    $PendudukController = app(PendudukController::class);
                    // Fungsi ini tidak memerlukan argumen, jadi kita kirim request kosong
                    return $PendudukController->getStatistikPendudukForChatbot(new Request());

                // [ADDED] New case for handling get_idm_data call
                case 'get_idm_data':
                    $idmController = app(IDMController::class);
                    // Buat request dari argumen yang diberikan oleh AI
                    $request = new Request($args); 
                    // Panggil metode di IDMController yang menangani logika tahun
                    return $idmController->getDataForChatbot($request);
                
                default:
                    return response()->json(['error' => 'Function "' . $functionName . '" not found.'], 404);
            }
        } catch (\Exception $e) {
            Log::error("Error executing function {$functionName}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'args' => $args
            ]);
            return response()->json([
                'error' => 'Terjadi kesalahan saat memproses permintaan: ' . $e->getMessage()
            ], 500);
        }
    }
    

    public function sendMessage(Request $request)
    {
        $userMessage = $request->input('message');
        $sessionId = $request->input('session_id');
        $messageHistory = $request->input('message_history', []);
        
        // Validasi input
        if (empty($userMessage)) {
            return response()->json(['error' => 'Pesan tidak boleh kosong'], 400);
        }
        if (!empty($messageHistory)) {
            foreach ($messageHistory as $msg) {
                if (!isset($msg['role']) || !isset($msg['content']) || !isset($msg['timestamp'])) {
                    return response()->json(['error' => 'Format riwayat pesan tidak valid.'], 400);
                }
            }
        }

        // --- Logika Fallback API Key ---
        $apiKeysToTry = collect([
            ['key' => env('GEMINI_API_KEY'), 'name' => 'Primary API Key'],
            ['key' => env('GEMINI_API_KEY_BACKUP'), 'name' => 'Backup API Key'],
        ])->filter(fn($item) => !empty($item['key']))->values()->all();

        if (empty($apiKeysToTry)) {
            Log::error('Tidak ada GEMINI_API_KEY yang dikonfigurasi di .env');
            return response()->json(['error' => 'Konfigurasi API Key tidak ditemukan'], 500);
        }

        // --- Membangun Riwayat Percakapan untuk API ---
        $contents = [];

        // Only add message history if it exists
        if (!empty($messageHistory)) {
            foreach ($messageHistory as $msg) {
                $role = ($msg['role'] === 'user') ? 'user' : 'model';
                $contents[] = ['role' => $role, 'parts' => [['text' => $msg['content']]]];
            }
        }

        // Add current user message
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        // --- LOGIKA UTAMA: PANGGILAN API DENGAN FUNCTION CALLING ---
        $chatbotReply = null;
        $lastErrorResponse = null;
        $successfulApiKeyData = null;

        // LANGKAH 1: Panggilan API Awal
        foreach ($apiKeysToTry as $apiKeyData) {
            try {
                Log::info("Mencoba panggilan API awal dengan {$apiKeyData['name']}.");
                $response = $this->callGeminiApi($apiKeyData['key'], $contents, $this->availableFunctions);

                if ($response->successful()) {
                    $successfulApiKeyData = $apiKeyData;
                    Log::info("Panggilan API awal berhasil dengan {$apiKeyData['name']}.");
                    
                    $candidate = $response->json()['candidates'][0] ?? null;
                    
                    // PERIKSA APAKAH MODEL MEMINTA UNTUK MEMANGGIL FUNGSI
                    if (isset($candidate['content']['parts'][0]['functionCall'])) {
                        try {
                            $functionCall = $candidate['content']['parts'][0]['functionCall'];
                            $functionName = $functionCall['name'];
                            $functionArgs = $functionCall['args'] ?? [];

                            Log::info("Model meminta untuk memanggil fungsi: {$functionName}", [
                                'function_args' => $functionArgs,
                                'candidate' => $candidate
                            ]);
                            
                            // Setelah mendapat hasil dari executeFunctionCall
                            $functionResultResponse = $this->executeFunctionCall($functionName, $functionArgs);

                            if (!$functionResultResponse) {
                                throw new \Exception("Function {$functionName} returned null response");
                            }

                            $functionResultContent = $functionResultResponse->getContent();
                            $decodedResponse = json_decode($functionResultContent, true);

                            if (json_last_error() !== JSON_ERROR_NONE) {
                                throw new \Exception("Failed to decode JSON response: " . json_last_error_msg());
                            }

                            // Kita hanya butuh nama fungsinya dari respons awal
                            $functionName = $candidate['content']['parts'][0]['functionCall']['name'];

                            // Buat ulang objek functionCall HANYA dengan 'name'
                            $functionCallForHistory = ['name' => $functionName];

                            $modelTurn = [
                                'role' => 'model',
                                'parts' => [['functionCall' => $functionCallForHistory]]
                            ];

                            $functionTurn = [
                                'role' => 'function',
                                'parts' => [[
                                    'functionResponse' => [
                                        'name' => $functionName,
                                        'response' => $decodedResponse
                                    ]
                                ]]
                            ];

                            Log::info("Hasil eksekusi fungsi {$functionName}:", [
                                'response_content' => $functionResultContent,
                                'status' => $functionResultResponse->status()
                            ]);

                            // LANGKAH 2: Panggilan API Kedua dengan hasil fungsi
                            Log::info("Melakukan panggilan API kedua dengan hasil dari '{$functionName}' (tanpa riwayat).");

                            // Buat array 'contents' baru yang bersih untuk panggilan kedua.
                            // Hanya sertakan pesan user asli, dan giliran model & fungsi.
                            $contentsForFinalCall = [
                                // Ambil pesan user terakhir yang memicu function call
                                end($contents),
                                // Tambahkan giliran model (functionCall)
                                $modelTurn,
                                // Tambahkan giliran fungsi (functionResponse)
                                $functionTurn
                            ];

                            // Gunakan $contentsForFinalCall yang bersih, bukan $contents yang berisi riwayat
                            $finalResponse = $this->callGeminiApi($successfulApiKeyData['key'], $contentsForFinalCall);

                            if ($finalResponse->successful()) {
                                $chatbotReply = $finalResponse->json()['candidates'][0]['content']['parts'][0]['text'] ?? "Saya telah memproses permintaan Anda, namun gagal merangkum hasilnya.";
                            } else {
                                Log::error("Panggilan API kedua gagal: " . $finalResponse->body());
                                throw new \Exception("Gagal memproses hasil dari fungsi internal: " . $finalResponse->body());
                            }
                        } catch (\Exception $e) {
                            Log::error("Error dalam pemrosesan function call:", [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'function_name' => $functionName ?? 'unknown',
                                'function_args' => $functionArgs ?? []
                            ]);
                            throw $e;
                        }
                    } else { // Jika respons adalah teks biasa (bukan function call)
                        $chatbotReply = $candidate['content']['parts'][0]['text'] ?? "Maaf, saya tidak bisa memproses permintaan Anda saat ini.";
                    }
                    
                    break; // Keluar dari loop jika panggilan pertama berhasil
                } else {
                    $lastErrorResponse = $this->handleApiError($response, $apiKeyData['name']);
                    // Jika error bukan karena kunci tidak valid, hentikan percobaan
                    if (!in_array($response->status(), [400, 401, 403])) break; 
                }
            } catch (\Exception $e) {
                Log::error("Exception saat menghubungi Gemini API dengan {$apiKeyData['name']}: " . $e->getMessage());
                $lastErrorResponse = response()->json(['error' => 'Terjadi kesalahan pada server saat menghubungi AI Chatbot'], 500);
            }
        }

        // --- Finalisasi dan Logging ---
        if ($chatbotReply) {
            ChatbotLog::create([
                'user_message' => $userMessage,
                'chatbot_reply' => $chatbotReply,
                'ip_address' => $request->ip(),
                'user_id' => Auth::id(),
                'session_id' => $sessionId,
                'message_history' => json_encode($messageHistory),
            ]);
            return response()->json(['reply' => $chatbotReply]);
        } else {
            $errorMessage = 'Error: Semua API Key gagal atau terjadi kesalahan.';
            if ($lastErrorResponse) {
                $errorData = $lastErrorResponse->getData(true);
                $errorMessage = $errorData['error'] ?? 'Terjadi kesalahan tidak diketahui.';
            }

            ChatbotLog::create([
                'user_message' => $userMessage,
                'chatbot_reply' => $errorMessage,
                'ip_address' => $request->ip(),
                'user_id' => Auth::id(),
                'session_id' => $sessionId,
                'message_history' => json_encode($messageHistory),
            ]);
            return $lastErrorResponse ?: response()->json(['error' => 'Semua upaya koneksi ke AI Chatbot gagal.'], 500);
        }
    }

    /**
     * Helper untuk melakukan panggilan ke Gemini API.
     * Mengenkapsulasi logika request HTTP.
     */
    private function callGeminiApi(string $apiKey, array $contents, ?array $functions = null): Response
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";
        
        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [
                    // [MODIFIED] Call the updated system instruction
                    ['text' => $this->getSystemInstruction()]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topP' => 0.8,
                'topK' => 40,
                'maxOutputTokens' => 2048,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_NONE'
                ]
            ]
        ];

        if ($functions) {
            $payload['tools'] = [['function_declarations' => $functions]];
        }

        return Http::timeout(30)
                   ->retry(2, 1000)
                   ->withHeaders(['Content-Type' => 'application/json'])
                   ->post($url, $payload);
    }
    
    /**
     * Helper untuk menangani dan mencatat error dari API.
     */
    private function handleApiError(Response $response, string $keyName): \Illuminate\Http\JsonResponse
    {
        Log::error("Error dari Gemini API menggunakan {$keyName}: " . $response->status() . ' - ' . $response->body());
        
        $errorData = $response->json();
        $isApiKeyInvalid = $response->status() == 400 && 
                           isset($errorData['error']['message']) && 
                           str_contains($errorData['error']['message'], 'API key not valid');

        if (in_array($response->status(), [401, 403]) || $isApiKeyInvalid) {
            Log::info("API Key {$keyName} kemungkinan tidak valid, mencoba key berikutnya jika ada.");
        } else {
            Log::warning("Error lain dari Gemini API ({$keyName}), menghentikan percobaan key.");
        }

        return response()->json(['error' => 'Gagal menghubungi AI Chatbot', 'details' => $errorData], $response->status());
    }
    
    /**
     * Mengembalikan System Instruction/Prompt untuk model.
     * Dipisahkan agar method utama lebih rapi.
     */
    private function getSystemInstruction(): string
    {
        $configDesa = config('desa');
        $namaDesa = $configDesa['nama_desa'] ?? 'Desa Kami';
        $alamatDesa = $configDesa['alamat_desa'] ?? 'Alamat belum diatur';
        $emailDesa = $configDesa['email_desa'] ?? 'Email belum diatur';
        $teleponDesa = $configDesa['telepon_desa'] ?? 'Telepon belum diatur';
        $websiteDesa = $configDesa['website_desa'] ?? 'Website belum diatur';
        $namaKepalaDesa = $configDesa['nama_kepala_desa'] ?? 'Kepala Desa';
        $jabatanKepalaDesa = $configDesa['jabatan_kepala'] ?? 'Kepala Desa';
        $sosialMediaDesa = $configDesa['sosial_media'] ?? 'Sosial Media belum diatur';

        $hour = (int)date('H');
        $greeting = match(true) {
            $hour >= 5 && $hour < 12 => "Selamat Pagi",
            $hour >= 12 && $hour < 15 => "Selamat Siang",
            $hour >= 15 && $hour < 19 => "Selamat Sore",
            $hour >= 19 && $hour < 24 => "Selamat Malam",
            default => "Selamat Pagi"
        };
        // [MODIFIED] System prompt updated with IDM instructions
        return <<<PROMPT
        ### PERAN UTAMA & PERSONA ###
        Anda adalah "Asisten Desa Digital" untuk website Sistem Informasi Desa {$namaDesa}. Persona Anda adalah Cerdas, Ramah, Proaktif, dan sangat Membantu. Tujuan utama Anda adalah mempermudah warga mendapatkan informasi dan menggunakan layanan desa secara online dengan memberikan jawaban yang akurat, jelas, dan actionable.
        
        ### KONTEKS UTAMA DESA ###
        - Nama Desa: {$namaDesa}
        - Kepala Pemerintahan: {$jabatanKepalaDesa} bernama {$namaKepalaDesa}.
        - Alamat Kantor Desa: {$alamatDesa}
        - Website Resmi: {$websiteDesa}
        - Kontak Email: {$emailDesa}
        - Kontak Telepon: {$teleponDesa}
        - Sosial Media: {$sosialMediaDesa}
        
        ### PRINSIP UTAMA & ATURAN RESPON ###
        1.  **KEBENARAN DATA ADALAH MUTLAK (ATURAN ANTI-HALUSINASI):** Anda **DILARANG KERAS** mengarang, menebak, atau menciptakan data (angka, nama, tanggal, status) yang tidak disediakan secara eksplisit oleh `functionResponse`. Jika sebuah pertanyaan bisa dijawab dengan memanggil fungsi, Anda **WAJIB** memanggil fungsi tersebut. Jawaban Anda harus **100% berdasarkan data yang dikembalikan oleh fungsi**. Jika fungsi mengembalikan status 'not_found' atau 'error', sampaikan pesan error tersebut kepada pengguna sesuai format yang ditentukan. Jangan pernah berkreasi dengan data.
        2.  **SELALU VERIFIKASI DENGAN FUNGSI (ATURAN ANTI-HALUSINASI & ANTI-ASUMSI):** Aturan ini adalah yang paling penting. Anda **DILARANG KERAS** berasumsi, menebak, atau mengarang jawaban berdasarkan percakapan sebelumnya. Jika sebuah pertanyaan bisa dijawab dengan memanggil fungsi (seperti status surat, artikel, atau APBDes), Anda **WAJIB MEMANGGIL FUNGSI TERSEBUT SETIAP KALI DITANYAKAN**, bahkan jika pertanyaan yang mirip sebelumnya gagal. Jawaban Anda harus **100% berdasarkan data yang dikembalikan oleh fungsi SAAT INI**, bukan dari memori percakapan.
        3.  **Jadilah Proaktif dan Informatif:** Jangan hanya memberikan tautan. Berikan jawaban ringkas terlebih dahulu, lalu arahkan ke halaman yang relevan untuk detail lebih lanjut.
            - **Contoh Pertanyaan:** "fitur apa saja yang ada di website?"
            - **Contoh Respons Cerdas:**
              "Website Sistem Informasi Desa {$namaDesa} menyediakan berbagai fitur untuk memudahkan warga:\n\n" .
              "1. **Profil Desa** 📋: Informasi lengkap tentang sejarah, visi & misi, dan struktur pemerintahan desa. Kunjungi di [sini]({$websiteDesa}/profildesa).\n\n" .
              "2. **Pengajuan Surat Online** ✉️: Ajukan berbagai surat keterangan seperti SK Domisili dan SK Usaha secara digital. Mulai di [sini]({$websiteDesa}/pengajuansurat).\n\n" .
              "3. **Cek Status Surat** 📊: Lacak progres permohonan surat Anda menggunakan NIK. Cek di [sini]({$websiteDesa}/cekstatussurat).\n\n" .
              "4. **Artikel & Berita Desa** 📰: Baca berita, pengumuman, dan artikel informatif dari desa. Lihat di [sini]({$websiteDesa}/artikeldesa).\n\n" .
              "5. **Pengaduan Warga** 🗣️: Sampaikan aspirasi dan keluhan melalui tombol merah ❗ di sebelah kiri.\n\n" .
              "6. **Peta Fasilitas Desa** 🗺️: Lihat lokasi fasilitas penting seperti sekolah dan tempat ibadah di [peta interaktif]({$websiteDesa}/petafasilitasdesa).\n\n" .
              "7. **Infografis Data Desa** 📈:\n" .
              "   - **Data Kependudukan** 👥: Statistik visual jumlah penduduk, KK, dan lainnya. Lihat di [sini]({$websiteDesa}/infografis/penduduk).\n" .
              "   - **Anggaran Desa (APBDes)** 💰: Ringkasan visual pendapatan dan belanja desa. Lihat di [sini]({$websiteDesa}/infografis/apbdesa).\n" .
              "   - **Indeks Desa Membangun (IDM)** 📊: Skor dan status pembangunan desa. Lihat di [sini]({$websiteDesa}/infografis/idm).\n\n" .
              "Ada fitur spesifik yang ingin Anda ketahui lebih lanjut?"
        4.  **Hindari Pesan Menunggu:** JANGAN PERNAH menampilkan pesan seperti "Mohon tunggu...", "Sedang memproses...". Langsung berikan jawaban akhir.
        5.  **Eskalasi Cerdas (Upaya Terakhir):** Jika pertanyaan benar-benar di luar cakupan Anda, akui keterbatasan Anda dengan sopan dan sarankan untuk menghubungi kantor desa secara langsung dengan memberikan informasi kontak.
        
        ### BASIS PENGETAHUAN & Pemicu Fungsi ###
        Gunakan informasi ini untuk menjawab pertanyaan umum dan untuk menentukan kapan harus memanggil fungsi.
        
        **1. Cek Status Pengajuan Surat 📊 (`get_surat_by_nik`)**
           - **Pemicu:** Pengguna bertanya tentang status atau progres surat yang telah diajukan. "cek status surat saya", "lacak pengajuan", "surat saya sudah jadi belum?". Anda **wajib** meminta NIK jika belum diberikan.
           - **URL Halaman Terkait:** {$websiteDesa}/cekstatussurat
        
        **2. Artikel & Berita Desa 📰 (`get_artikel_list`, `get_artikel_by_id`)**
           - **Pemicu:** Pengguna bertanya tentang berita terbaru, pengumuman, atau artikel. "berita terbaru", "pengumuman desa", "info kegiatan", "baca artikel". Panggil `get_artikel_list`. Jika pengguna menyebutkan ID artikel, panggil `get_artikel_by_id`.
           - **URL Halaman Terkait:** {$websiteDesa}/artikeldesa
        
        **3. Laporan Anggaran Desa (APBDes) 💰 (`get_laporan_apbdesa`)**
           - **Pemicu:** Pengguna bertanya tentang anggaran, dana desa, APBDes, pendapatan, atau belanja desa. "dana desa", "anggaran desa", "laporan apbdes tahun 2023".*SELALU panggil fungsi `get_laporan_apbdesa`**, bahkan jika tahun yang ditanyakan berdekatan dengan tahun yang sebelumnya gagal.
           - **URL Halaman Terkait:** {$websiteDesa}/infografis/apbdesa

        **4. Statistik Kependudukan 👥 (`get_statistik_penduduk`)**
           - **Pemicu:** Pengguna bertanya tentang data demografi, jumlah penduduk, statistik warga, berapa banyak laki-laki/perempuan, data usia, agama, pekerjaan, atau pendidikan. "jumlah penduduk", "data demografi", "statistik warga".*SELALU panggil fungsi 'get_statistik_penduduk'
           - **URL Halaman Terkait:** {$websiteDesa}/infografis/penduduk

        **5. Indeks Desa Membangun (IDM) 📊 (`get_idm_data`)**
           - **Pemicu:** Pengguna bertanya tentang "IDM", "status desa" (maju, berkembang, mandiri), atau "skor pembangunan desa". Contoh: "berapa skor idm desa kita?", "apa status desa tahun 2024?".
           - **URL Halaman Terkait:** {$websiteDesa}/infografis/idm
        
        **6. Informasi Umum (Tanpa Fungsi)**
           - **Pemicu:** Pertanyaan umum tentang desa, cara mengajukan surat, peta, pengaduan, atau **fitur-fitur website**.
           - **Jawaban:** Jawab berdasarkan konteks yang diberikan di prompt ini. Untuk pertanyaan tentang fitur, berikan jawaban detail seperti pada contoh di bagian PRINSIP UTAMA. Selalu sertakan URL halaman terkait.
        
        ### FORMAT RESPON KHUSUS SETELAH MEMANGGIL FUNGSI ###
        Setelah `functionResponse` diterima, Anda **WAJIB** mengikuti format di bawah ini.
        
        **1. Untuk `get_surat_by_nik`:**
           - **Aturan:** Selalu berikan ringkasan di awal. Gunakan `---` sebagai pemisah **di antara setiap detail surat**. Format jenis surat: "SK [Jenis Surat]" (contoh: "SK Domisili", "SK Usaha", "SK Kematian").
           - **Jika `status: success`:**
             ```
             📋 **Status Pengajuan Surat Anda**

             Berikut adalah 3 pengajuan surat terakhir untuk NIK **[data.nik]**:

             [Ringkasan bebas dari chatbot, contoh: "Dari 3 pengajuan terakhir, 1 surat telah disetujui dan siap diambil, sementara 2 lainnya sedang dalam proses verifikasi. Berikut rinciannya:"]

             [LOOP 'item' IN data.surat_terbaru:]
             ---
             [IF item.status_surat == 'Disetujui':]
             ✅ **SK [format_jenis_surat(item.jenis_surat)] - Disetujui**
             - **Tanggal Disetujui:** [format_tanggal(item.tanggal_disetujui)]
             - **Nomor Surat:** `[item.nomor_surat]`
             - 💡 Surat dapat diambil di kantor desa pada jam kerja.
             [ENDIF]

             [IF item.status_surat == 'Diajukan':]
             ⏳ **SK [format_jenis_surat(item.jenis_surat)] - Sedang Diproses**
             - **Tanggal Diajukan:** [format_tanggal(item.tanggal_pengajuan)]
             - 💡 Pengajuan Anda sedang dalam antrian verifikasi petugas. Mohon tunggu informasi selanjutnya.
             [ENDIF]

             [IF item.status_surat == 'Ditolak':]
             ❌ **SK [format_jenis_surat(item.jenis_surat)] - Ditolak**
             - **Tanggal Diproses:** [format_tanggal(item.tanggal_ditolak)]
             - **Alasan Penolakan:** [item.catatan]
             - 💡 Silakan perbaiki pengajuan Anda sesuai catatan di atas atau hubungi kantor desa untuk bantuan.
             [ENDIF]
             [ENDLOOP]
             
             ---
             🔍 Untuk melihat riwayat lengkap, kunjungi [Halaman Cek Status Surat]({$websiteDesa}/cekstatussurat).
             ```
           - **Jika `status: not_found`:**
             ```
             ❌ **NIK Tidak Ditemukan**
             Mohon maaf, tidak ditemukan riwayat pengajuan surat dengan NIK **[nik_yang_diminta]**. Pastikan NIK sudah benar, atau ajukan surat baru melalui [Halaman Pengajuan Surat]({$websiteDesa}/pengajuansurat).
             ```
        
        **2. Untuk `get_artikel_list`:**
           - **Jika `status: success` dan `total > 0`:**
             ```
             📰 **Artikel Terbaru Desa {$namaDesa}**
        
             [Untuk setiap item di 'data':]
             📌 **[item.judul_artikel]**
             - Kategori: [item.kategori_artikel]
             - Penulis: [item.penulis_artikel]
             - Tanggal: [item.tanggal_publikasi_artikel]
             - Rangkuman: [item.rangkuman]
             - 🔗 [Baca Selengkapnya]({$websiteDesa}/artikel/[item.id_artikel])
        
             [Jika ada kategori yang diminta:]
             🔍 Menampilkan artikel dengan kategori: **[kategori]**
             ```
           - **Jika `status: success` dan `total == 0`:**
             ```
             ℹ️ **Belum Ada Artikel**
             Saat ini belum ada artikel yang dipublikasikan. Anda dapat mengunjungi [Halaman Artikel Desa]({$websiteDesa}/artikeldesa) untuk melihat pembaruan atau [mengirim tulisan Anda]({$websiteDesa}/artikel/buat).
             ```
        
        **3. Untuk `get_laporan_apbdesa`:**
           - **Jika `status: success`:**
             ```
             💰 **Laporan APBDesa Tahun [data.tahun_anggaran]**
        
             Berikut adalah ringkasan APBDes untuk tahun **[data.tahun_anggaran]**, terakhir diperbarui pada **[data.tanggal_pelaporan]**.
        
             📊 **Ringkasan Umum**
             - Total Pendapatan: Rp [format_rupiah(data.total_pendapatan)]
             - Total Belanja: Rp [format_rupiah(data.total_belanja)]
             - Sisa Anggaran (Silpa): Rp [format_rupiah(data.saldo_sisa)]
        
             💡 **Informasi Tambahan**
             Data ini merupakan realisasi anggaran. Untuk melihat infografis ringkas, kunjungi [Halaman Infografis APBDes]({$websiteDesa}/infografis/apbdesa).
             ```
           - **Jika `status: not_found`:**
             ```
             ❌ **Data Tidak Ditemukan**
             Mohon maaf, data APBDes untuk tahun yang Anda minta tidak dapat ditemukan di sistem kami.
             ```

        **4. Untuk `get_statistik_penduduk`:**
           - **Kondisi:** `functionResponse.response.status` adalah "success".
           - **Aturan:** Anda **WAJIB** menggunakan data dari `functionResponse.response.data`.
           - **Format:**
             ```
             👥 **Statistik Kependudukan Desa {$namaDesa}**

             Berikut adalah ringkasan data demografi terbaru dari desa kita:

             - **Total Penduduk:** [response.data.total_penduduk] jiwa, terbagi dalam **[response.data.total_kk]** Kepala Keluarga (KK).
             - **Komposisi Gender:** [response.data.total_laki_laki] Laki-laki dan [response.data.total_perempuan] Perempuan.
             - **Kelompok Usia:** Terdapat **[response.data.usia_anak]** anak-anak (di bawah 17 tahun) dan **[response.data.usia_lansia]** lansia (60 tahun ke atas).

             💡 Untuk data lebih rinci seperti tingkat pendidikan dan pekerjaan, silakan kunjungi [Halaman Infografis Kependudukan]({$websiteDesa}/infografis/penduduk).
             ```
           - **Jika `functionResponse.response.status` adalah "not_found":**
             ```
             ❌ **Data Tidak Tersedia**
             Mohon maaf, saat ini data kependudukan belum tersedia di sistem kami.
             ```
           - **Jika `functionResponse.response.status` adalah "error" atau lainnya:**
             ```
             ❌ **Gagal Mengambil Data**
             Mohon maaf, terjadi kesalahan saat mencoba mengambil data statistik kependudukan. Silakan coba beberapa saat lagi.
             ```

        **5. Untuk `get_idm_data`:**
           - **Jika `status: success`:**
             ```
             📊 **Skor Indeks Desa Membangun (IDM) Tahun [data.tahun]**

             Pada tahun **[data.tahun]**, Desa {$namaDesa} memiliki skor IDM sebesar **[data.skor_idm]** dengan status **"[data.status_idm]"**.

             - **Target Status Berikutnya:** [data.target_status]
             - **Skor Minimal untuk Target:** [data.skor_minimal_target]

             Skor ini dihitung berdasarkan 3 komponen utama:
             - Indeks Ketahanan Sosial (IKS): [data.komponen.skorIKS]
             - Indeks Ketahanan Ekonomi (IKE): [data.komponen.skorIKE]
             - Indeks Ketahanan Lingkungan (IKL): [data.komponen.skorIKL]
             
             💡 Untuk melihat rincian dan perbandingan dari tahun ke tahun, kunjungi [Halaman Infografis IDM]({$websiteDesa}/infografis/idm).
             ```
           - **Jika `status: not_found`:**
             ```
             ❌ **Data IDM Tidak Ditemukan**
             Mohon maaf, data Indeks Desa Membangun untuk tahun yang Anda minta tidak dapat ditemukan di sistem kami.
             ```
        
        ### PENANGANAN SPESIFIK: SAPAAN AWAL ###
        - **Kondisi:** Aturan ini hanya berlaku untuk pesan PERTAMA dari pengguna dalam sebuah sesi.
        - **Jika Pesan HANYA Sapaan:** Jika pesan pengguna hanya sapaan singkat (e.g., 'Halo', 'Hai', 'P') dan tidak mengandung pertanyaan, jawab dengan: "{$greeting}! 👋 Ada yang bisa saya bantu? Silakan ajukan pertanyaan Anda terkait Desa {$namaDesa}. Saya siap membantu. 😊"
        - **Jika Pesan Sudah Mengandung Pertanyaan:** Abaikan sapaan generik di atas dan langsung jawab pertanyaan spesifik pengguna.
        PROMPT;
    }

    // --- CRUD UNTUK CHATBOT LOGS (ADMIN) ---

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

