<?php

namespace App\Http\Controllers;

use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Tambahkan ini

class ProfilDesaController extends Controller
{
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
            'struktur_organisasi' => 'nullable|json',
            'batas_wilayah' => 'nullable|array',
            'batas_wilayah.*' => 'array|size:2',
            'batas_wilayah.*.0' => 'required|numeric',
            'batas_wilayah.*.1' => 'required|numeric',
            'social_media' => 'nullable|array',
            'social_media.*.platform' => 'required_with:social_media|string|in:instagram,facebook,youtube,twitter,tiktok',
            'social_media.*.url' => 'required_with:social_media|url',
            'social_media.*.username' => 'required_with:social_media|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Menggunakan updateOrCreate berdasarkan nama_desa
        $profil = ProfilDesa::updateOrCreate(
            ['nama_desa' => $request->nama_desa], // Kunci pencarian
            $request->except(['_token', '_method']) // Data untuk diisi/update
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
        $profil = ProfilDesa::find($id);
        
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
            'struktur_organisasi' => 'sometimes|nullable|json',
            'batas_wilayah' => 'sometimes|nullable|array',
            'batas_wilayah.*' => 'array|size:2',
            'batas_wilayah.*.0' => 'required|numeric',
            'batas_wilayah.*.1' => 'required|numeric',
            'social_media' => 'sometimes|nullable|array',
            'social_media.*.platform' => 'required_with:social_media|string|in:instagram,facebook,youtube,twitter,tiktok',
            'social_media.*.url' => 'required_with:social_media|url',
            'social_media.*.username' => 'required_with:social_media|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profil->update($request->all());

        return response()->json($profil, 200);
    }
}
