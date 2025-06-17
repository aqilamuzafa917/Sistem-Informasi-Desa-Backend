<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Log;

class PengaduanController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pengaduan = Pengaduan::all();
        return response()->json($pengaduan);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:15',
            'kategori' => 'required|string|max:50',
            'detail_pengaduan' => 'required|string',
            'media' => 'array|nullable',
            'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        
        $mediaFiles = [];

        if ($request->hasFile('media')) {
            Log::info('Starting upload for pengaduan-media files', [
                'total_files' => count($request->file('media'))
            ]);

            foreach ($request->file('media') as $index => $file) {
                try {
                    $uploadResult = $this->supabaseService->uploadPengaduanMedia($file);
                    $signedUrl = $this->supabaseService->getPengaduanMediaUrl($uploadResult);

                    $mediaFiles[] = [
                        'path' => $uploadResult['path'] ?? null,
                        'type' => $file->getClientMimeType(),
                        'name' => $file->getClientOriginalName(),
                        'url' => $signedUrl
                    ];
                } catch (\Exception $e) {
                    Log::error("Failed to upload media file {$index}", [
                        'error' => $e->getMessage()
                    ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload file: ' . $e->getMessage()
                    ], 500);
                }
            }

            Log::info("Finished uploading all pengaduan media", [
                'uploaded_files' => count($mediaFiles)
            ]);
        }
        $validatedData['status'] = 'Diajukan';
        $validatedData['media'] = $mediaFiles;
        

        $pengaduan = Pengaduan::create($validatedData);

        return response()->json($pengaduan, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengaduan $pengaduan)
    {
        return response()->json($pengaduan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $pengaduan->update([
            'status' => $request->input('status'),
        ]);

        return response()->json($pengaduan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengaduan $pengaduan)
    {
        $pengaduan->delete();

        return response()->json(null, 204);
    }

    public function filterByKategori(Request $request)
    {
        $query = trim($request->input('query'));
        if (!$query) {
            return response()->json([]);
        }

        $pengaduan = Pengaduan::where('kategori', 'LIKE', "%$query%")->get();

        return response()->json($pengaduan);
    }

    public function getStatistikPengaduan()
    {
        $totalPengaduan = Pengaduan::count();
        $totalDiajukan = Pengaduan::where('status', 'Diajukan')->count();
        $totalDiterima = Pengaduan::where('status', 'Diterima')->count();
        $totalDitolak = Pengaduan::where('status', 'Ditolak')->count();

        return response()->json([
            'total_pengaduan' => $totalPengaduan,
            'total_diajukan' => $totalDiajukan,
            'total_diterima' => $totalDiterima,
            'total_ditolak' => $totalDitolak,
        ]);
    }

    public function filterByStatus(Request $request)
    {
        $status = $request->input('status');
        if (!$status) {
            return response()->json([]);
        }

        $pengaduan = Pengaduan::where('status', $status)->get();

        return response()->json($pengaduan);
    }
}
