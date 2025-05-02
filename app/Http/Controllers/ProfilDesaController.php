<?php

namespace App\Http\Controllers;

use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfilDesaController extends Controller
{
    /**
     * Display a listing of the village profiles.
     * Mengambil semua profil desa yang ada.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Ambil semua data profil desa
        $profils = ProfilDesa::all();

        if ($profils->isEmpty()) {
            return response()->json(['message' => 'Belum ada profil desa'], 404);
        }

        return response()->json($profils);
    }

    /**
     * Display the specified village profile by name.
     * Pencarian case-insensitive dan menangani underscore sebagai spasi.
     *
     * @param  string  $nama_desa_url (Nama desa dari URL, bisa mengandung underscore)
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByName(string $nama_desa_url)
    {
        // Ganti underscore dengan spasi dan decode URL
        $nama_desa_decoded = urldecode(str_replace('_', ' ', $nama_desa_url));

        // Cari profil desa berdasarkan nama_desa (case-insensitive)
        $profil = ProfilDesa::whereRaw('LOWER(nama_desa) = ?', [strtolower($nama_desa_decoded)])->first();

        if (!$profil) {
            return response()->json(['message' => 'Profil desa tidak ditemukan'], 404);
        }

        return response()->json($profil);
    }

    /**
     * Store a newly created or update existing village profile in storage.
     * Menggunakan updateOrCreate untuk menangani pembuatan baru atau pembaruan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_desa' => 'required|string|max:255|unique:profil_desas,nama_desa,' . ($request->id ?? 'NULL') . ',id',
            'sejarah' => 'nullable|string',
            'tradisi_budaya' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'peta_lokasi' => 'nullable|string|max:255', // Bisa URL atau path
            'alamat_kantor' => 'nullable|string',
            'struktur_organisasi' => 'nullable|json', // Add validation for JSON
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Gunakan updateOrCreate berdasarkan nama_desa
        $profil = ProfilDesa::updateOrCreate(
            ['nama_desa' => $request->nama_desa],
            $validator->validated() // validated() now includes struktur_organisasi
        );

        return response()->json([
            'message' => 'Profil desa berhasil disimpan.',
            'data' => $profil
        ], 201);
    }

    // Anda bisa menambahkan method lain seperti show, update (dengan PUT/PATCH), destroy jika diperlukan
    // public function show($id) { ... }
    // public function update(Request $request, $id) { ... }
    // public function destroy($id) { ... }
}
