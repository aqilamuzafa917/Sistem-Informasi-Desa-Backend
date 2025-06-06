<?php

namespace App\Http\Controllers;

use App\Models\ProfilDesa;
use App\Services\SupabaseService;
use App\Exceptions\StrukturOrganisasiException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProfilDesaController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Display a listing of the resource.
     * (GET /profil - Public)
     */
    public function index()
    {
        $profils = ProfilDesa::all();
        return response()->json($profils);
    }

    /**
     * Store a newly created or update an existing resource in storage.
     * (POST /profil - Requires Auth)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_desa' => 'required|string',
            'sejarah' => 'nullable|string',
            'tradisi_budaya' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'peta_lokasi' => 'nullable|string',
            'alamat_kantor' => 'nullable|string',
            'struktur_organisasi' => 'nullable|array',
            'struktur_organisasi.*' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'batas_wilayah' => 'nullable|array',
            'batas_wilayah.utara' => 'nullable|string',
            'batas_wilayah.timur' => 'nullable|string',
            'batas_wilayah.selatan' => 'nullable|string',
            'batas_wilayah.barat' => 'nullable|string',
            'luas_desa' => 'nullable|numeric',
            'polygon_desa' => 'nullable|array',
            'polygon_desa.*' => 'array|size:2',
            'polygon_desa.*.*' => 'numeric',
            'social_media' => 'nullable|array',
            'social_media.*.platform' => 'required_with:social_media|string|in:instagram,facebook,youtube,twitter,tiktok',
            'social_media.*.url' => 'required_with:social_media|url',
            'social_media.*.username' => 'required_with:social_media|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except(['struktur_organisasi']);

        
        // Handle struktur_organisasi file upload
        if ($request->hasFile('struktur_organisasi')) {
            $mediaFiles = [];
            foreach ($request->file('struktur_organisasi') as $file) {
                try {
                    Log::info('Processing struktur_organisasi file', [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'size' => $file->getSize()
                    ]);

                    // Upload file to Supabase
                    $uploadResult = $this->supabaseService->uploadProfilMedia($file);
                    
                    // Get signed URL
                    $signedUrl = $this->supabaseService->getProfilMediaUrl($uploadResult);
                    
                    // Store file information
                    $mediaFiles[] = [
                        'path' => $uploadResult['path'] ?? null,
                        'type' => $file->getClientMimeType(),
                        'name' => $file->getClientOriginalName(),
                        'url' => $signedUrl
                    ];

                    Log::info('File uploaded successfully', [
                        'path' => $uploadResult['path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error uploading file', [
                        'error' => $e->getMessage(),
                        'file' => $file->getClientOriginalName()
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload file struktur organisasi: ' . $e->getMessage()
                    ], 500);
                }
            }
            $data['struktur_organisasi'] = $mediaFiles;
        }

        // Menggunakan updateOrCreate berdasarkan nama_desa
        $profil = ProfilDesa::updateOrCreate(
            ['nama_desa' => $request->nama_desa],
            $data
        );

        return response()->json($profil, $profil->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource by name (case-insensitive, underscore as space).
     * (GET /profil/{nama_desa} - Public)
     */
    public function showByName(string $nama_desa)
    {
        // Normalisasi input: ganti underscore dengan spasi, lowercase
        $normalizedName = strtolower(str_replace('_', ' ', $nama_desa));

        // Cari di database dengan perbandingan lowercase
        $profil = ProfilDesa::whereRaw('LOWER(nama_desa) = ?', [$normalizedName])->first();

        if (!$profil) {
            return response()->json(['message' => 'Profil desa tidak ditemukan'], 404);
        }

        return response()->json($profil);
    }

    /**
     * Remove the specified resource from storage by name.
     * (DELETE /profil/{nama_desa} - Requires Auth)
     */
    public function destroyByName(string $nama_desa)
    {
        // Normalisasi input seperti pada showByName
        $normalizedName = strtolower(str_replace('_', ' ', $nama_desa));

        $profil = ProfilDesa::whereRaw('LOWER(nama_desa) = ?', [$normalizedName])->first();

        if (!$profil) {
            return response()->json(['message' => 'Profil desa tidak ditemukan'], 404);
        }

        // Hanya admin yang bisa akses ini (sudah dihandle middleware)
        $profil->delete();

        return response()->json(['message' => 'Profil desa berhasil dihapus'], 200); // atau 204 No Content
    }

    /**
     * Remove the specified resource from storage by ID.
     * (DELETE /profil/{id} - Requires Auth) - Alternatif jika menggunakan ID
     */
    public function destroy(string $id)
    {
        $profil = ProfilDesa::find($id);
        if (!$profil) {
            return response()->json(['message' => 'Profil desa tidak ditemukan'], 404);
        }

        // Hanya admin yang bisa akses ini (sudah dihandle middleware)
        $profil->delete();

        return response()->json(['message' => 'Profil desa berhasil dihapus'], 200); // atau 204 No Content
    }

    /**
     * Display the specified resource by ID.
     * (GET /profil-desa/{id} - Public)
     */
    public function show(string $id)
    {
        $profil = ProfilDesa::where('id', $id)->first();
        
        if (!$profil) {
            return response()->json(['message' => 'Profil desa tidak ditemukan'], 404);
        }

        return response()->json($profil);
    }

    /**
     * Get nama_desa by ID.
     * (GET /profil-desa/{id}/nama - Public)
     */
    public function getNamaDesa(string $id)
    {
        $profil = ProfilDesa::find($id);
        
        if (!$profil) {
            return response()->json(['message' => 'Profil desa tidak ditemukan'], 404);
        }

        return response()->json([
            'nama_desa' => $profil->nama_desa,
            'social_media' => $profil->social_media
        ]);
    }

    /**
     * Update specific fields of the specified resource.
     * (PATCH /profil/{id} - Requires Auth)
     */
    public function update(Request $request, string $id)
    {
        $profil = ProfilDesa::find($id);
        if (!$profil) {
            return response()->json(['message' => 'Profil desa tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_desa' => 'sometimes|required|string',
            'sejarah' => 'sometimes|nullable|string',
            'tradisi_budaya' => 'sometimes|nullable|string',
            'visi' => 'sometimes|nullable|string',
            'misi' => 'sometimes|nullable|string',
            'peta_lokasi' => 'sometimes|nullable|string',
            'alamat_kantor' => 'sometimes|nullable|string',
            'struktur_organisasi' => 'sometimes|nullable|array',
            'struktur_organisasi.*' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'batas_wilayah' => 'sometimes|nullable|array',
            'batas_wilayah.utara' => 'nullable|string',
            'batas_wilayah.timur' => 'nullable|string',
            'batas_wilayah.selatan' => 'nullable|string',
            'batas_wilayah.barat' => 'nullable|string',
            'luas_desa' => 'sometimes|nullable|numeric',
            'polygon_desa' => 'sometimes|nullable|array',
            'polygon_desa.*' => 'array|size:2',
            'polygon_desa.*.*' => 'numeric',
            'social_media' => 'sometimes|nullable|array',
            'social_media.*.platform' => 'required_with:social_media|string|in:instagram,facebook,youtube,twitter,tiktok',
            'social_media.*.url' => 'required_with:social_media|url',
            'social_media.*.username' => 'required_with:social_media|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except(['struktur_organisasi']);

        // Handle struktur_organisasi file upload
        if ($request->hasFile('struktur_organisasi')) {
            $mediaFiles = [];
            foreach ($request->file('struktur_organisasi') as $file) {
                try {
                    Log::info('Processing struktur_organisasi file', [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'size' => $file->getSize()
                    ]);

                    // Upload file to Supabase
                    $uploadResult = $this->supabaseService->uploadProfilMedia($file);
                    
                    // Get signed URL
                    $signedUrl = $this->supabaseService->getProfilMediaUrl($uploadResult);
                    
                    // Store file information
                    $mediaFiles[] = [
                        'path' => $uploadResult['path'] ?? null,
                        'type' => $file->getClientMimeType(),
                        'name' => $file->getClientOriginalName(),
                        'url' => $signedUrl
                    ];

                    Log::info('File uploaded successfully', [
                        'path' => $uploadResult['path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error uploading file', [
                        'error' => $e->getMessage(),
                        'file' => $file->getClientOriginalName()
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload file struktur organisasi: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Delete old files if exists
            if (!empty($profil->struktur_organisasi)) {
                foreach ($profil->struktur_organisasi as $oldFile) {
                    if (isset($oldFile['path'])) {
                        try {
                            $this->supabaseService->deleteProfilMedia($oldFile['path']);
                        } catch (\Exception $e) {
                            Log::error('Error deleting old file', [
                                'error' => $e->getMessage(),
                                'path' => $oldFile['path']
                            ]);
                        }
                    }
                }
            }

            $data['struktur_organisasi'] = $mediaFiles;
        }

        $profil->update($data);

        return response()->json($profil, 200);
    }

    /**
     * Update struktur_organisasi field only.
     * (PATCH /profil/{id}/struktur-organisasi - Requires Auth)
     */
    public function updateStrukturOrganisasi(Request $request, string $id)
    {
        $profil = ProfilDesa::find($id);
        if (!$profil) {
            return response()->json(['message' => 'Profil desa tidak ditemukan'], 404);
        }

        if (!$request->hasFile('struktur_organisasi')) {
            throw StrukturOrganisasiException::fileNotFound();
        }

        $files = $request->file('struktur_organisasi');
        if (!is_array($files)) {
            $files = [$files]; // Convert single file to array
        }

        $mediaFiles = [];
        $errors = [];

        foreach ($files as $file) {
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file->getClientMimeType(), $allowedTypes)) {
                $errors[] = "File {$file->getClientOriginalName()} harus berformat JPEG, PNG, atau JPG";
                continue;
            }

            // Validate file size (2MB = 2 * 1024 * 1024 bytes)
            if ($file->getSize() > 2 * 1024 * 1024) {
                $errors[] = "File {$file->getClientOriginalName()} terlalu besar (maksimal 2MB)";
                continue;
            }

            try {
                Log::info('Processing struktur_organisasi file', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize()
                ]);

                // Upload file to Supabase
                $uploadResult = $this->supabaseService->uploadProfilMedia($file);
                
                // Get signed URL
                $signedUrl = $this->supabaseService->getProfilMediaUrl($uploadResult);
                
                // Store file information
                $mediaFiles[] = [
                    'path' => $uploadResult['path'] ?? null,
                    'type' => $file->getClientMimeType(),
                    'name' => $file->getClientOriginalName(),
                    'url' => $signedUrl
                ];

                Log::info('File uploaded successfully', [
                    'path' => $uploadResult['path']
                ]);
            } catch (\Exception $e) {
                Log::error('Error uploading file', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName()
                ]);
                $errors[] = "Gagal mengupload file {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        // If there are any errors and no successful uploads, throw exception
        if (!empty($errors) && empty($mediaFiles)) {
            throw StrukturOrganisasiException::uploadFailed(implode(', ', $errors));
        }

        // Delete old files if exists
        if (!empty($profil->struktur_organisasi)) {
            foreach ($profil->struktur_organisasi as $oldFile) {
                if (isset($oldFile['path'])) {
                    try {
                        $this->supabaseService->deleteProfilMedia($oldFile['path']);
                    } catch (\Exception $e) {
                        Log::error('Error deleting old file', [
                            'error' => $e->getMessage(),
                            'path' => $oldFile['path']
                        ]);
                        // Don't throw exception here, just log the error
                    }
                }
            }
        }

        // Update the profile with new files
        $profil->struktur_organisasi = $mediaFiles;
        $profil->save();

        $response = [
            'status' => 'success',
            'data' => $profil,
            'message' => 'Struktur organisasi berhasil diperbarui'
        ];

        // If there were any errors, add them to the response
        if (!empty($errors)) {
            $response['warnings'] = $errors;
        }

        return response()->json($response);
    }
}
