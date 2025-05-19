<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArtikelController extends Controller
{
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
    // Di dalam method store
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
            'media_artikel.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,pdf,doc,docx|max:10240',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Proses upload media jika ada
          // Proses upload media jika ada
        $mediaFiles = [];
        if ($request->hasFile('media_artikel')) {
            foreach ($request->file('media_artikel') as $file) {
                // Simpan file ke storage
                $path = $file->store('artikel/media', 'public');
                
                // Simpan informasi file
                $mediaFiles[] = [
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                    'name' => $file->getClientOriginalName(),
                    // 'url' => '/storage/' . $path  // Tambahkan URL relatif
                ];
            }
        }
    
        // Buat artikel baru
        // Status dan tanggal publikasi akan diatur otomatis oleh model jika jenis_artikel adalah resmi
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
            // Status dan tanggal publikasi akan diatur oleh event model
        ]);
    
        return response()->json([
            'status' => 'success',
            'data' => $artikel,
            'message' => 'Artikel berhasil dibuat'
        ], 201);
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
            'media_artikel.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Proses upload media baru jika ada
        if ($request->hasFile('media_artikel')) {
            $mediaFiles = [];
            foreach ($request->file('media_artikel') as $file) {
                $path = $file->store('artikel/media', 'public');
                $mediaFiles[] = [
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                    'name' => $file->getClientOriginalName()
                ];
            }
            
            // Hapus media lama jika ada
            if (!empty($artikel->media_artikel)) {
                foreach ($artikel->media_artikel as $media) {
                    if (isset($media['path'])) {
                        Storage::disk('public')->delete($media['path']);
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
                    Storage::disk('public')->delete($media['path']);
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
            'media_artikel.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Proses upload media jika ada
        $mediaFiles = [];
        if ($request->hasFile('media_artikel')) {
            foreach ($request->file('media_artikel') as $file) {
                // Simpan file ke storage
                $path = $file->store('artikel/media', 'public');
                
                // Simpan informasi file dengan URL relatif
                $mediaFiles[] = [
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                    'name' => $file->getClientOriginalName(),
                    // 'url' => '/storage/' . $path  // Tambahkan URL relatif
                ];
            }
        }
    
        // Buat artikel baru dengan jenis 'warga' dan status 'diajukan'
        $artikel = Artikel::create([
            'jenis_artikel' => 'warga', // Tetapkan jenis artikel sebagai 'warga'
            'status_artikel' => 'diajukan', // Tetapkan status artikel sebagai 'diajukan'
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
}
