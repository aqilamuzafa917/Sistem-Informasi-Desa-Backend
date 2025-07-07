<?php

namespace App\Http\Controllers; // Pastikan namespace benar

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use App\Models\Surat;          // Import model Surat
use Barryvdh\DomPDF\Facade\Pdf; // Import facade PDF
use App\Models\Penduduk;      // Asumsi ada model Penduduk
use Illuminate\Validation\Rule; // Import Rule untuk validasi
use Illuminate\Support\Facades\Validator; // Import Validator untuk validasi kondisional
use Illuminate\Support\Facades\DB; // Tambahkan ini jika belum ada

class SuratController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Simpan pengajuan surat baru.
     * (POST /surat)
     */
    public function store(Request $request)
    {
        // --- Validasi Dasar ---
        $baseRules = [
            'nik_pemohon' => [
                'required',
                Rule::exists('penduduk', 'nik') // Pastikan NIK ada di tabel penduduk
            ],
            'jenis_surat' => 'required|string|max:100', // Jenis surat menentukan validasi lain
            'keperluan' => 'required|string|max:500',
            'tanggal_pengajuan' => 'sometimes|required|date', // Bisa otomatis diisi
            'attachment_bukti_pendukung' => 'nullable|array',
            'attachment_bukti_pendukung.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Maksimal 2MB (default PHP)
        ];

        // --- Validasi Kondisional Berdasarkan Jenis Surat ---
        $jenisSurat = $request->input('jenis_surat');
        $conditionalRules = [];

        switch ($jenisSurat) {
            case 'SK_KEMATIAN':
                $conditionalRules = [
                    'nik_penduduk_meninggal' => ['required', 'string', 'digits:16', Rule::exists('penduduk', 'nik')],
                    'tanggal_kematian' => 'required|date',
                    'waktu_kematian' => 'required|date_format:H:i', // Format jam:menit
                    'tempat_kematian' => 'required|string|max:255',
                    'penyebab_kematian' => 'required|string|max:255',
                    'hubungan_pelapor_kematian' => 'required|string|max:100',
                ];
                break;
            case 'SK_PINDAH':
                $conditionalRules = [
                    'no_kk_pemohon' => [ 'nullable','string', 'size:16', Rule::exists('penduduk', 'no_kk')], // Tambahkan ini
                    'alamat_tujuan' => 'required|string|max:255',
                    'rt_tujuan' => 'required|string|max:5',
                    'rw_tujuan' => 'required|string|max:5',
                    'kelurahan_desa_tujuan' => 'required|string|max:100',
                    'kecamatan_tujuan' => 'required|string|max:100',
                    'kabupaten_kota_tujuan' => 'required|string|max:100',
                    'provinsi_tujuan' => 'required|string|max:100',
                    'alasan_pindah' => 'required|string|max:255',
                    'klasifikasi_pindah' => 'required|string|max:100',
                    'data_pengikut_pindah' => 'nullable|array', // Validasi lebih detail jika diperlukan
                    'data_pengikut_pindah.*.nik' => 'required_with:data_pengikut_pindah|string|size:16', // Contoh validasi nested array
                ];
                break;

            case 'SK_KELAHIRAN':
                $conditionalRules = [
                     'nama_bayi' => 'required|string|max:255',
                     'tempat_dilahirkan' => 'required|string|max:100', // RS, Rumah, dll.
                     'tempat_kelahiran' => 'required|string|max:100', // Kota/Kabupaten
                     'tanggal_lahir_bayi' => 'required|date',
                     'waktu_lahir_bayi' => 'required|date_format:H:i',
                     'jenis_kelamin_bayi' => 'required|in:Laki-laki,Perempuan',
                     'jenis_kelahiran' => 'required|string|max:50', // Tunggal, Kembar 2, dll.
                     'anak_ke' => 'required|integer|min:1',
                     'penolong_kelahiran' => 'required|string|max:100', // Dokter, Bidan, Dukun, dll.
                     'berat_bayi_kg' => 'required|numeric|min:0',
                     'panjang_bayi_cm' => 'required|numeric|min:0',
                     'nik_penduduk_ibu' => ['required', 'string', 'digits:16', Rule::exists('penduduk', 'nik')],
                     'nik_penduduk_ayah' => ['nullable', 'string', 'digits:16', Rule::exists('penduduk', 'nik')],
    
                ];
                break;

            case 'SK_USAHA':
                $conditionalRules = [
                    'nama_usaha' => 'required|string|max:255',
                    'jenis_usaha' => 'required|string|max:100',
                    'alamat_usaha' => 'required|string|max:500',
                    'status_bangunan_usaha' => 'nullable|string|max:100',
                    'perkiraan_modal_usaha' => 'nullable|numeric|min:0',
                    'perkiraan_pendapatan_usaha' => 'nullable|numeric|min:0',
                    'jumlah_tenaga_kerja' => 'nullable|integer|min:0',
                    'sejak_tanggal_usaha' => 'nullable|date',
                 ];
                 break;
            case 'REKOM_KIP':
            case 'SKTM': 
                $conditionalRules = [
                    'penghasilan_perbulan_kepala_keluarga' => 'required|integer|min:0',
                ];
                break;
            case 'KARTU_INDONESIA_PINTAR':      // Gabungkan jika validasinya sama
                $conditionalRules = [
                    'penghasilan_perbulan_kepala_keluarga' => 'required|integer|min:0',
                    'pekerjaan_kepala_keluarga' => 'required|string|max:255',
                    // Validasi untuk KIP
                    'nik_penduduk_siswa' => ['required_if:jenis_surat,KARTU_INDONESIA_PINTAR', 'nullable', Rule::exists('penduduk', 'nik')],
                    'nama_sekolah' => 'required_if:jenis_surat,KARTU_INDONESIA_PINTAR|nullable|string|max:255',
                    'nisn_siswa' => 'required_if:jenis_surat,KARTU_INDONESIA_PINTAR|nullable|string|digits_between:10,10', // NISN biasanya 10 digit
                ];
                break;

            case 'SK_KEHILANGAN_KTP':
                 $conditionalRules = [
                    'nomor_ktp_hilang' => 'required|string|size:16', // NIK KTP biasanya 16 digit
                    'tanggal_perkiraan_hilang' => 'required|date|before_or_equal:today',
                    'lokasi_perkiraan_hilang' => 'required|string|max:255',
                    'kronologi_singkat' => 'required|string|max:1000',
                    'nomor_laporan_polisi' => 'nullable|string|max:100', // Mungkin belum ada saat pengajuan
                    'tanggal_laporan_polisi' => 'nullable|date|before_or_equal:today', // Mungkin belum ada saat pengajuan
                 ];
                 break;

            // Tambahkan case untuk jenis surat lainnya di sini
            // ...

            default:
                // Tidak ada validasi tambahan jika jenis surat tidak dikenali atau umum
                break;
        }

        // Gabungkan rules dan validasi
        $validator = Validator::make($request->all(), array_merge($baseRules, $conditionalRules));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        // Ambil data yang sudah divalidasi
        $validatedData = $validator->validated();

        // Hapus status_surat dari input jika ada (untuk keamanan)
        if (isset($request['status_surat'])) {
            unset($request['status_surat']);
        }

        // Set status awal - selalu "Diajukan" dan tidak bisa diubah oleh input
        $validatedData['status_surat'] = 'Diajukan'; // Status awal saat diajukan
        $validatedData['tanggal_pengajuan'] = $validatedData['tanggal_pengajuan'] ?? now(); // Isi tanggal request jika tidak ada
       
        // Handle file upload ke Supabase jika ada
        $attachmentFiles = [];
        if ($request->hasFile('attachment_bukti_pendukung')) {
            foreach ($request->file('attachment_bukti_pendukung') as $file) {
                try {
                    // Upload file ke Supabase
                    $uploadResult = $this->supabaseService->uploadSuratBuktiPendukung($file);
                    
                    // Get the URL and ensure it's properly encoded
                    $url = $this->supabaseService->getSuratBuktiPendukungUrl($uploadResult);
                    
                    // Simpan informasi file
                    $attachmentFiles[] = [
                        'path' => $uploadResult['path'] ?? null,
                        'type' => $file->getClientMimeType(),
                        'name' => $file->getClientOriginalName(),
                        'url' => $url
                    ];
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload bukti pendukung: ' . $e->getMessage()
                    ], 500);
                }
            }
        }

        // Set attachment_bukti_pendukung ke array file jika ada
        $validatedData['attachment_bukti_pendukung'] = !empty($attachmentFiles) ? $attachmentFiles : null;

        // Buat record surat
        $surat = Surat::create($validatedData);

        // $surat->nomor_surat sekarang sudah terisi otomatis oleh model

        return response()->json([
            'message' => 'Pengajuan surat berhasil dibuat',
            'data' => $surat->load('pemohon') // Muat relasi pemohon jika perlu ditampilkan
        ], 201);
    }

    /**
     * Tampilkan daftar surat (Admin).
     * (GET /surat)
     */
    public function index(Request $request)
    {
        // Query dasar
        $query = Surat::with('pemohon')->latest(); // Eager load pemohon

        // Filter berdasarkan status (opsional)
        if ($request->has('status')) {
            $query->where('status_surat', $request->input('status'));
        }

        // Filter berdasarkan jenis surat (opsional)
        if ($request->has('jenis')) {
            $query->where('jenis_surat', $request->input('jenis'));
        }

        // Tambahkan paginasi
        $surat = $query->paginate(15); // Ambil 15 data per halaman

        return response()->json($surat);
    }

    /**
     * Tampilkan detail surat spesifik.
     * (GET /surat/{id_surat})
     */
     public function show(string $id)
     {
         // Cari surat berdasarkan ID beserta relasi yang mungkin diperlukan
         $surat = Surat::with([
                'pemohon',
                'pendudukMeninggal',
                'ibuBayi',
                'ayahBayi',
                'siswa'
             ])->find($id); // Gunakan find agar null jika tidak ketemu

         if (!$surat) {
             return response()->json(['message' => 'Surat tidak ditemukan'], 404);
         }

         return response()->json($surat);
     }


    /**
     * Tampilkan daftar surat berdasarkan NIK Pemohon.
     * (GET /surat/nik/{nik})
     */
    public function showByNik(string $nik)
    {
        if (!ctype_digit($nik) || strlen($nik) !== 16) {
             return response()->json(['message' => 'Format NIK tidak valid. Harus 16 digit angka.'], 400);
        }

        // Gunakan nik_pemohon dan tambahkan paginasi jika perlu
        $surat = Surat::where('nik_pemohon', $nik)->latest()->paginate(10);

        if ($surat->isEmpty()) {
            return response()->json(['message' => 'Tidak ada surat ditemukan untuk NIK ini'], 404);
        }

        return response()->json($surat);
    }

    public function latestShowByNik(string $nik)
    {
        if (!ctype_digit($nik) || strlen($nik) !== 16) {
             return response()->json(['message' => 'Format NIK tidak valid. Harus 16 digit angka.'], 400);
        }

        // Get total count of surat records for this NIK
        $totalSurat = Surat::where('nik_pemohon', $nik)->count();

        // Ambil 3 surat terbaru berdasarkan nik_pemohon dengan pagination
        $surat = Surat::where('nik_pemohon', $nik)
                     ->latest()
                     ->paginate(3);

        if ($surat->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada surat ditemukan untuk NIK ini',
                'total_surat' => 0
            ], 404);
        }

        // Add total count to the response
        $response = $surat->toArray();
        $response['total_surat'] = $totalSurat;

        return response()->json($response);
    }

    public function update(Request $request, string $id)
    {
        $surat = Surat::findOrFail($id);

        // --- Tanpa Validasi Kompleks di Backend (Andalkan Frontend/Input Admin) ---
        // Ambil semua data dari request
        $inputData = $request->all();

        // Hapus field yang seharusnya tidak boleh diupdate dari input
        unset(
            $inputData['id_surat'],
            $inputData['nomor_surat'],
            $inputData['jenis_surat'],
            $inputData['nik_pemohon'],
            $inputData['created_at'],
            $inputData['updated_at'],
            $inputData['attachment_bukti_pendukung']
        );

        // Proses upload attachment baru jika ada
        if ($request->hasFile('attachment_bukti_pendukung')) {
            // Hapus attachment lama jika ada
            if (!empty($surat->attachment_bukti_pendukung)) {
                foreach ($surat->attachment_bukti_pendukung as $attachment) {
                    if (isset($attachment['path'])) {
                        $this->supabaseService->deleteSuratBuktiPendukung($attachment['path']);
                    }
                }
            }
            
            // Simpan file baru ke Supabase
            $attachmentFiles = [];
            foreach ($request->file('attachment_bukti_pendukung') as $file) {
                try {
                    // Upload file ke Supabase
                    $uploadResult = $this->supabaseService->uploadSuratBuktiPendukung($file);
                    
                    // Get the URL and ensure it's properly encoded
                    $url = $this->supabaseService->getSuratBuktiPendukungUrl($uploadResult);
                    
                    // Simpan informasi file
                    $attachmentFiles[] = [
                        'path' => $uploadResult['path'] ?? null,
                        'type' => $file->getClientMimeType(),
                        'name' => $file->getClientOriginalName(),
                        'url' => $url
                    ];
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal mengupload bukti pendukung: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            // Update attachment_bukti_pendukung dengan array baru
            $surat->attachment_bukti_pendukung = $attachmentFiles;
        }
        
        $surat->fill($inputData);
        $surat->save();

        return response()->json([
            'message' => 'Data surat berhasil diperbarui',
            'data' => $surat->fresh()->load('pemohon')
        ]);
    }

    /**
     * Update status surat (Approval/Rejection oleh Admin).
     * (PUT/PATCH /surat/{id_surat}/status) - Lebih baik gunakan route spesifik
     */
    
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status_surat' => ['required', Rule::in(['Disetujui', 'Ditolak'])], // Gunakan status dari model
            'catatan' => 'nullable|string|max:1000'
        ]);

        $surat = Surat::findOrFail($id); // findOrFail akan otomatis 404 jika tidak ketemu

        // Gunakan metode di model jika ada logika tambahan, jika tidak set langsung
        if ($request->status_surat === 'Disetujui') {
            // Generate nomor surat saat approval
            if (empty($surat->nomor_surat)) {
                // Generate nomor surat menggunakan metode di model
                $surat->nomor_surat = Surat::generateNomorSurat($surat->jenis_surat);
            }
            
            $surat->status_surat = 'Disetujui';
            $surat->tanggal_disetujui = now()->toDateString(); // Set tanggal approval
        } else {
            $surat->status_surat = 'Ditolak';
            $surat->tanggal_disetujui = null; // Hapus tanggal approval jika ditolak
        }

        // Set catatan 
        if ($request->has('catatan')) {
            $surat->catatan = $request->catatan;
        }

        $surat->save();
    

        return response()->json([
            'message' => 'Status surat berhasil diperbarui',
            'data' => $surat
        ]);
    }

    /**
     * Generate PDF untuk surat yang sudah disetujui (User, dengan verifikasi NIK dan tanggal lahir).
     * (GET /surat/pdf/{nik}/{id_surat})
     */
    public function generatePDF(string $nik, string $id)
    {
        try {
            // Validasi NIK
            if (!ctype_digit($nik) || strlen($nik) !== 16) {
                return response()->json(['message' => 'Format NIK tidak valid. Harus 16 digit angka.'], 400);
            }

            // Ambil tanggal_lahir dari query string
            $tanggal_lahir = request()->input('tanggal_lahir');
            if (!$tanggal_lahir) {
                return response()->json(['message' => 'Tanggal lahir wajib diisi.'], 400);
            }

            // Cek ke tabel penduduk
            $penduduk = \App\Models\Penduduk::where('nik', $nik)
                ->whereDate('tanggal_lahir', $tanggal_lahir)
                ->first();
            if (!$penduduk) {
                return response()->json(['message' => 'Tanggal lahir tidak sesuai dengan NIK.'], 403);
            }

            // Eager load relasi yang dibutuhkan di PDF
            $surat = Surat::with([
                'pemohon',
                'pendudukMeninggal',
                'ibuBayi',
                'ayahBayi',
                'siswa'
            ])->findOrFail($id); // Otomatis 404 jika tidak ada

            // Periksa apakah NIK pemohon cocok
            if ($surat->nik_pemohon !== $nik) {
                return response()->json(['message' => 'Akses ditolak. NIK tidak sesuai dengan data surat.'], 403); // Forbidden
            }

            // Periksa status surat (Gunakan status_surat)
            if ($surat->status_surat !== 'Disetujui' && $surat->status_surat !== 'Printed') {
                return response()->json(['message' => 'Surat belum disetujui atau tidak valid untuk diunduh'], 403); // Forbidden
            }

            // Tentukan view berdasarkan jenis surat
            $viewName = 'pdf.templates.' . Str::lower($surat->jenis_surat);
            if (!view()->exists($viewName)) {
                $viewName = 'pdf.surat_generic';
                if (!view()->exists($viewName)) {
                    return response()->json(['message' => 'Template PDF tidak ditemukan.'], 500);
                }
            }

            // Load view spesifik dengan data surat
            $pdf = Pdf::loadView($viewName, compact('surat'))->setPaper('F4', 'portrait');

            // Buat nama file yang deskriptif
            $filename = 'SURAT_'
                . Str::upper(Str::slug($surat->jenis_surat, '_')) . '_'
                . Str::upper(Str::slug($surat->pemohon->nama ?? $surat->nik_pemohon, '_')) . '_'
                . $surat->getKey()
                . '.pdf';

            // Tawarkan download
            return $pdf->download($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Surat dengan ID tersebut tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat membuat PDF surat'], 500);
        }
    }

    /**
     * Generate PDF untuk surat (Admin, tanpa verifikasi tanggal lahir/NIK).
     * (GET /surat/{id}/pdf)
     */
    public function adminGeneratePDF(string $id)
    {
        try {
            // Eager load relasi yang dibutuhkan di PDF
            $surat = Surat::with([
                'pemohon',
                'pendudukMeninggal',
                'ibuBayi',
                'ayahBayi',
                'siswa'
            ])->findOrFail($id); // Otomatis 404 jika tidak ada

            // Periksa status surat (Gunakan status_surat)
            if ($surat->status_surat !== 'Disetujui' && $surat->status_surat !== 'Printed') {
                return response()->json(['message' => 'Surat belum disetujui atau tidak valid untuk diunduh'], 403);
            }

            // Tentukan view berdasarkan jenis surat
            $viewName = 'pdf.templates.' . Str::lower($surat->jenis_surat);
            if (!view()->exists($viewName)) {
                $viewName = 'pdf.surat_generic';
                if (!view()->exists($viewName)) {
                    return response()->json(['message' => 'Template PDF tidak ditemukan.'], 500);
                }
            }

            $pdf = Pdf::loadView($viewName, compact('surat'))->setPaper('F4', 'portrait');
            $filename = 'SURAT_'
                . Str::upper(Str::slug($surat->jenis_surat, '_')) . '_'
                . Str::upper(Str::slug($surat->pemohon->nama ?? $surat->nik_pemohon, '_')) . '_'
                . $surat->getKey()
                . '.pdf';

            return $pdf->download($filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Surat dengan ID tersebut tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Gagal generate PDF (admin): ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat membuat PDF surat'], 500);
        }
    }

    /**
     * Soft delete surat (hanya menandai sebagai dihapus).
     * (PUT/PATCH /surat/{id_surat}/delete)
     */
    public function softDelete(string $id)
    {
        try {
            $surat = Surat::findOrFail($id);
            
            // Cek apakah surat sudah dihapus sebelumnya
            if ($surat->trashed()) {
                return response()->json([
                    'message' => 'Surat sudah dihapus sebelumnya',
                    'id_surat' => $id
                ], 422);
            }
            
            $surat->delete(); // Ini akan melakukan soft delete karena model menggunakan SoftDeletes trait

            return response()->json([
                'message' => 'Surat berhasil dihapus (soft delete)',
                'id_surat' => $id
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Surat dengan ID tersebut tidak ditemukan',
                'error' => 'not_found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus surat: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus surat',
                'error' => 'server_error'
            ], 500);
        }
    }

    /**
     * Menampilkan daftar surat yang telah dihapus (soft deleted).
     * (GET /surat/trash)
     */
    public function trash(Request $request)
    {
        try {
            // Query dasar untuk surat yang sudah di-soft delete
            $query = Surat::onlyTrashed()->with('pemohon')->latest();

            // Filter berdasarkan status (opsional)
            if ($request->has('status')) {
                $query->where('status_surat', $request->input('status'));
            }

            // Filter berdasarkan jenis surat (opsional)
            if ($request->has('jenis')) {
                $query->where('jenis_surat', $request->input('jenis'));
            }

            // Tambahkan paginasi
            $surat = $query->paginate(15);

            // Cek apakah ada data
            if ($surat->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada surat yang dihapus',
                    'data' => $surat
                ]);
            }

            return response()->json($surat);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil daftar surat terhapus: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil daftar surat terhapus',
                'error' => 'server_error'
            ], 500);
        }
    }

    /**
     * Mengembalikan surat yang telah dihapus (restore).
     * (PUT/PATCH /surat/{id_surat}/restore)
     */
    public function restore(string $id)
    {
        try {
            $surat = Surat::onlyTrashed()->findOrFail($id);
            
            // Cek apakah surat sudah di-restore sebelumnya
            if (!$surat->trashed()) {
                return response()->json([
                    'message' => 'Surat tidak dalam status terhapus',
                    'id_surat' => $id
                ], 422);
            }
            
            $surat->restore();

            return response()->json([
                'message' => 'Surat berhasil dipulihkan',
                'data' => $surat->fresh()->load('pemohon')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Surat terhapus dengan ID tersebut tidak ditemukan',
                'error' => 'not_found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal memulihkan surat: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat memulihkan surat',
                'error' => 'server_error'
            ], 500);
        }
    }
    
    /**
     * Menghapus surat secara permanen (force delete).
     * (DELETE /surat/{id_surat}/force)
     */
    public function forceDelete(string $id)
    {
        try {
            $surat = Surat::withTrashed()->findOrFail($id);
            
            // Hapus attachment dari Supabase jika ada
            if (!empty($surat->attachment_bukti_pendukung)) {
                foreach ($surat->attachment_bukti_pendukung as $attachment) {
                    if (isset($attachment['path'])) {
                        $this->supabaseService->deleteSuratBuktiPendukung($attachment['path']);
                    }
                }
            }
            
            $surat->forceDelete();

            return response()->json([
                'message' => 'Surat berhasil dihapus secara permanen',
                'id_surat' => $id
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Surat dengan ID tersebut tidak ditemukan',
                'error' => 'not_found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus surat secara permanen: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus surat secara permanen',
                'error' => 'server_error'
            ], 500);
        }
    }


/**
 * Mengembalikan statistik terkait surat.
 * (GET /surat/stats) - Hanya untuk Admin
 */
public function getStats(Request $request)
{
    try {
        // Statistik keseluruhan
        $totalSurat = Surat::count();
        $diajukan = Surat::where('status_surat', 'Diajukan')->count();
        $disetujui = Surat::where('status_surat', 'Disetujui')->count();
        $ditolak = Surat::where('status_surat', 'Ditolak')->count();

        // Ambil semua jenis surat yang ada
        $jenisSurat = Surat::select('jenis_surat')
                        ->distinct()
                        ->pluck('jenis_surat');

        // Statistik per jenis surat
        $statistikPerJenis = [];
        foreach ($jenisSurat as $jenis) {
            $statistikPerJenis[] = [
                'jenis_surat' => $jenis,
                'jumlah' => Surat::where('jenis_surat', $jenis)->count(),
                'diajukan' => Surat::where('jenis_surat', $jenis)->where('status_surat', 'Diajukan')->count(),
                'disetujui' => Surat::where('jenis_surat', $jenis)->where('status_surat', 'Disetujui')->count(),
                'ditolak' => Surat::where('jenis_surat', $jenis)->where('status_surat', 'Ditolak')->count(),
            ];
        }

        // Statistik surat yang dihapus
        $suratDihapus = Surat::onlyTrashed()->count();

        return response()->json([
            'message' => 'Statistik surat berhasil diambil',
            'data' => [
                'total_surat' => $totalSurat,
                'status_diajukan' => $diajukan,
                'status_disetujui' => $disetujui,
                'status_ditolak' => $ditolak,
                'surat_per_jenis' => $statistikPerJenis,
                'total_surat_dihapus' => $suratDihapus,
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Gagal mengambil statistik surat: ' . $e->getMessage());
        return response()->json([
            'message' => 'Terjadi kesalahan saat mengambil statistik surat',
            'error' => $e->getMessage()
        ], 500);
    }
}
}