<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ArtikelController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Menampilkan daftar artikel.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Artikel::query();
        
        // Filter berdasarkan jenis artikel
        if ($request->has('jenis')) {
            $query->where('jenis_artikel', $request->jenis);
        }
        
        // Filter berdasarkan status artikel
        if ($request->has('status')) {
            $query->where('status_artikel', $request->status);
        }
        
        // Filter berdasarkan kategori
        if ($request->has('kategori')) {
            $query->where('kategori_artikel', $request->kategori);
        }
        
        // Pengurutan default berdasarkan tanggal terbaru
        $artikels = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $artikels,
            'message' => 'Daftar artikel berhasil diambil'
        ]);
    }

    /**
     * Menyimpan artikel baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_artikel' => 'required|in:resmi,warga',
            'judul_artikel' => 'required|string|max:255',
            'kategori_artikel' => 'required|string|max:100',
            'isi_artikel' => 'required|string',
            'penulis_artikel' => 'required|string|max:100',
            'tanggal_kejadian_artikel' => 'nullable|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'media_artikel' => 'nullable|array',
            'media_artikel.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,pdf,doc,docx|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Proses upload media ke Supabase jika ada
        $mediaFiles = [];
        if ($request->hasFile('media_artikel')) {
            Log::info('Starting media upload process', [
                'file_count' => count($request->file('media_artikel'))
            ]);

            foreach ($request->file('media_artikel') as $index => $file) {
                try {
                    Log::info("Processing file {$index}", [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'size' => $file->getSize()
                    ]);

                    // Upload file ke Supabase
                    $uploadResult = $this->supabaseService->uploadArtikelMedia($file);
                    
                    Log::info("File uploaded successfully", [
                        'path' => $uploadResult['path'] ?? null
                    ]);

                    // Get signed URL
                    $signedUrl = $this->supabaseService->getArtikelMediaUrl($uploadResult);
                    
                    Log::info("Got signed URL", [
                        'url' => $signedUrl
                    ]);
                    
                    // Simpan informasi file
                    $mediaFiles[] = [
                        'path' => $uploadResult['path'] ?? null,
                        'type' => $file->getClientMimeType(),
                        'name' => $file->getClientOriginalName(),
                        'url' => $signedUrl
                    ];

                    Log::info("File info added to mediaFiles array", [
                        'current_count' => count($mediaFiles)
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error uploading file {$index}", [
                        'error' => $e->getMessage(),
                        'file' => $file->getClientOriginalName()
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload media: ' . $e->getMessage()
                    ], 500);
                }
            }

            Log::info('Media upload process completed', [
                'total_files_processed' => count($mediaFiles)
            ]);
        }
    
        try {
            // Buat artikel baru
            $artikel = Artikel::create([
                'jenis_artikel' => $request->jenis_artikel,
                'judul_artikel' => $request->judul_artikel,
                'kategori_artikel' => $request->kategori_artikel,
                'isi_artikel' => $request->isi_artikel,
                'penulis_artikel' => $request->penulis_artikel,
                'tanggal_kejadian_artikel' => $request->tanggal_kejadian_artikel,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location_name' => $request->location_name,
                'media_artikel' => !empty($mediaFiles) ? $mediaFiles : null,
            ]);

            // Log the final response data
            Log::info('Final response data', [
                'artikel_id' => $artikel->id_artikel,
                'media_count' => count($artikel->media_artikel ?? []),
                'media_files' => $artikel->media_artikel
            ]);

            // Refresh the model to ensure we have the latest data
            $artikel->refresh();
        
            // Ensure we're returning the complete data
            return response()->json([
                'status' => 'success',
                'data' => [
                    'jenis_artikel' => $artikel->jenis_artikel,
                    'judul_artikel' => $artikel->judul_artikel,
                    'kategori_artikel' => $artikel->kategori_artikel,
                    'isi_artikel' => $artikel->isi_artikel,
                    'penulis_artikel' => $artikel->penulis_artikel,
                    'tanggal_kejadian_artikel' => $artikel->tanggal_kejadian_artikel,
                    'latitude' => $artikel->latitude,
                    'longitude' => $artikel->longitude,
                    'location_name' => $artikel->location_name,
                    'media_artikel' => $artikel->media_artikel,
                    'status_artikel' => $artikel->status_artikel,
                    'tanggal_publikasi_artikel' => $artikel->tanggal_publikasi_artikel,
                    'updated_at' => $artikel->updated_at,
                    'created_at' => $artikel->created_at,
                    'id_artikel' => $artikel->id_artikel
                ],
                'message' => 'Artikel berhasil dibuat'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating artikel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat artikel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan artikel tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $artikel = Artikel::find($id);
        
        if (!$artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $artikel,
            'message' => 'Detail artikel berhasil diambil'
        ]);
    }

    /**
     * Memperbarui artikel tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $artikel = Artikel::find($id);
        
        if (!$artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'jenis_artikel' => 'sometimes|required|in:resmi,warga',
            'status_artikel' => 'sometimes|required|in:diajukan,ditolak,disetujui',
            'judul_artikel' => 'sometimes|required|string|max:255',
            'kategori_artikel' => 'sometimes|required|string|max:100',
            'isi_artikel' => 'sometimes|required|string',
            'penulis_artikel' => 'sometimes|required|string|max:100',
            'tanggal_kejadian_artikel' => 'nullable|date',
            'tanggal_publikasi_artikel' => 'nullable|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'media_artikel' => 'nullable|array',
            'media_artikel.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Proses upload media baru ke Supabase jika ada
        if ($request->hasFile('media_artikel')) {
            $mediaFiles = [];
            foreach ($request->file('media_artikel') as $file) {
                try {
                    // Upload file ke Supabase
                    $uploadResult = $this->supabaseService->uploadArtikelMedia($file);
                    
                    // Simpan informasi file
                    $mediaFiles[] = [
                        'path' => $uploadResult['path'] ?? null,
                        'type' => $file->getClientMimeType(),
                        'name' => $file->getClientOriginalName(),
                        'url' => $this->supabaseService->getArtikelMediaUrl($uploadResult)
                    ];
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload media: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Hapus media lama jika ada
            if (!empty($artikel->media_artikel)) {
                foreach ($artikel->media_artikel as $media) {
                    if (isset($media['path'])) {
                        $this->supabaseService->deleteArtikelMedia($media['path']);
                    }
                }
            }
            
            $artikel->media_artikel = $mediaFiles;
        }

        // Update data artikel
        $artikel->fill($request->only([
            'jenis_artikel',
            'status_artikel',
            'judul_artikel',
            'kategori_artikel',
            'isi_artikel',
            'penulis_artikel',
            'tanggal_kejadian_artikel',
            'tanggal_publikasi_artikel',
            'location_name',
        ]));
        
        // Jika status diubah menjadi disetujui, set tanggal publikasi
        if ($request->has('status_artikel') && $request->status_artikel === 'disetujui' && !$artikel->tanggal_publikasi_artikel) {
            $artikel->tanggal_publikasi_artikel = now();
        }
        
        $artikel->save();

        return response()->json([
            'status' => 'success',
            'data' => $artikel,
            'message' => 'Artikel berhasil diperbarui'
        ]);
    }

    /**
     * Menghapus artikel tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $artikel = Artikel::find($id);
        
        if (!$artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }
        
        // Hapus media terkait jika ada
        if (!empty($artikel->media_artikel)) {
            foreach ($artikel->media_artikel as $media) {
                if (isset($media['path'])) {
                    $this->supabaseService->deleteArtikelMedia($media['path']);
                }
            }
        }
        
        $artikel->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Artikel berhasil dihapus'
        ]);
    }
    
    /**
     * Mengubah status artikel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status_artikel' => 'required|in:diajukan,ditolak,disetujui',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $artikel = Artikel::find($id);
        
        if (!$artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }
        
        $artikel->status_artikel = $request->status_artikel;
        
        // Jika status diubah menjadi disetujui, set tanggal publikasi
        if ($request->status_artikel === 'disetujui' && !$artikel->tanggal_publikasi_artikel) {
            $artikel->tanggal_publikasi_artikel = now();
        }
        
        $artikel->save();
        
        return response()->json([
            'status' => 'success',
            'data' => $artikel,
            'message' => 'Status artikel berhasil diperbarui'
        ]);
    }

    /**
     * Menampilkan daftar artikel untuk publik (hanya yang disetujui).
     *
     * @return \Illuminate\Http\Response
     */
    public function publicIndex(Request $request)
    {
        $query = Artikel::query();
        
        // Hanya tampilkan artikel yang sudah disetujui
        $query->where('status_artikel', 'disetujui');
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('kategori')) {
            $query->where('kategori_artikel', $request->kategori);
        }
        
        // Pengurutan default berdasarkan tanggal publikasi terbaru
        $artikels = $query->orderBy('tanggal_publikasi_artikel', 'desc')->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $artikels,
            'message' => 'Daftar artikel publik berhasil diambil'
        ]);
    }

    /**
     * Menampilkan artikel tertentu untuk publik (hanya yang disetujui).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publicShow($id)
    {
        $artikel = Artikel::where('id_artikel', $id)
                          ->where('status_artikel', 'disetujui')
                          ->first();
        
        if (!$artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan atau belum disetujui'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $artikel,
            'message' => 'Detail artikel berhasil diambil'
        ]);
    }

    /**
     * Menyimpan artikel baru dari warga (tanpa autentikasi).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publicStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_artikel' => 'required|string|max:255',
            'kategori_artikel' => 'required|string|max:100',
            'isi_artikel' => 'required|string',
            'penulis_artikel' => 'required|string|max:100',
            'tanggal_kejadian_artikel' => 'nullable|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'media_artikel' => 'nullable|array',
            'media_artikel.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Proses upload media ke Supabase jika ada
        $mediaFiles = [];
        if ($request->hasFile('media_artikel')) {
            foreach ($request->file('media_artikel') as $file) {
                try {
                    // Upload file ke Supabase
                    $uploadResult = $this->supabaseService->uploadArtikelMedia($file);
                    
                    // Simpan informasi file
                    $mediaFiles[] = [
                        'path' => $uploadResult['path'] ?? null,
                        'type' => $file->getClientMimeType(),
                        'name' => $file->getClientOriginalName(),
                        'url' => $this->supabaseService->getArtikelMediaUrl($uploadResult)
                    ];
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload media: ' . $e->getMessage()
                    ], 500);
                }
            }
        }
    
        // Buat artikel baru dengan jenis 'warga' dan status 'diajukan'
        $artikel = Artikel::create([
            'jenis_artikel' => 'warga',
            'status_artikel' => 'diajukan',
            'judul_artikel' => $request->judul_artikel,
            'kategori_artikel' => $request->kategori_artikel,
            'isi_artikel' => $request->isi_artikel,
            'penulis_artikel' => $request->penulis_artikel,
            'tanggal_kejadian_artikel' => $request->tanggal_kejadian_artikel,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name,
            'media_artikel' => !empty($mediaFiles) ? $mediaFiles : null,
        ]);
    
        return response()->json([
            'status' => 'success',
            'data' => $artikel,
            'message' => 'Artikel warga berhasil diajukan dan menunggu persetujuan'
        ], 201);
    }

    /**
     * Mendapatkan statistik artikel.
     *
     * @return \Illuminate\Http\Response
     */
    public function getArtikelStats()
    {
        $diajukanCount = Artikel::where('status_artikel', 'diajukan')->count();
        $disetujuiCount = Artikel::where('status_artikel', 'disetujui')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'diajukan' => $diajukanCount,
                'disetujui' => $disetujuiCount,
            ],
            'message' => 'Statistik artikel berhasil diambil'
        ]);
    }

    /**
     * Mendapatkan 5 artikel terbaru untuk chatbot.
     * Versi sederhana dari publicIndex yang hanya menampilkan 5 artikel terbaru.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function latestPublicIndex(Request $request)
    {
        $limit = 5; // Set limit to 5 articles as per documentation

        $query = Artikel::query()
            ->where('status_artikel', 'disetujui')
            ->select([
                'id_artikel',
                'judul_artikel',
                'tanggal_publikasi_artikel',
                'penulis_artikel',
                'kategori_artikel',
                'isi_artikel'
            ])
            ->orderBy('tanggal_publikasi_artikel', 'desc');

        // Filter berdasarkan kategori jika ada
        if ($request->has('kategori')) {
            $query->where('kategori_artikel', $request->kategori);
        }

        // Get 5 latest articles using take() and get()
        $artikels = $query->take($limit)->get();

        // Tambahkan rangkuman untuk setiap artikel menggunakan Gemini AI
        $artikels->transform(function ($artikel) {
            try {
                // Bersihkan konten dari HTML tags
                $isi = strip_tags($artikel->isi_artikel);
                
                // Siapkan prompt untuk Gemini
                $prompt = "Buat rangkuman singkat dan informatif dari artikel berikut dalam 2-3 kalimat:\n\n{$isi}";
                
                // Panggil Gemini API untuk membuat rangkuman
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . env('GEMINI_API_KEY'), [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topP' => 0.8,
                        'topK' => 40,
                        'maxOutputTokens' => 150,
                    ]
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $rangkuman = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    
                    if ($rangkuman) {
                        $artikel->rangkuman = $rangkuman;
                    } else {
                        // Fallback jika Gemini gagal membuat rangkuman
                        $artikel->rangkuman = substr($isi, 0, 150) . '...';
                    }
                } else {
                    // Fallback jika API call gagal
                    $artikel->rangkuman = substr($isi, 0, 150) . '...';
                }
            } catch (\Exception $e) {
                // Log error dan gunakan fallback
                Log::error('Error generating article summary:', [
                    'error' => $e->getMessage(),
                    'artikel_id' => $artikel->id_artikel
                ]);
                $artikel->rangkuman = substr($isi, 0, 150) . '...';
            }
            
            return $artikel;
        });

        return response()->json([
            'status' => 'success',
            'data' => $artikels,
            'total' => $artikels->count(),
            'message' => "Berikut adalah {$artikels->count()} artikel terbaru dari desa"
        ]);
    }
}
