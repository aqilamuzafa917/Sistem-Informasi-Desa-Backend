<?php

namespace App\Http\Controllers; // Pastikan namespace benar

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Surat;          // Import model Surat
use Barryvdh\DomPDF\Facade\Pdf; // Import facade PDF
use App\Models\Penduduk;      // Asumsi ada model Penduduk
use Illuminate\Validation\Rule; // Import Rule untuk validasi
use Illuminate\Support\Facades\Validator; // Import Validator untuk validasi kondisional

class SuratController extends Controller
{
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
                Rule::exists('penduduks', 'nik') // Pastikan NIK ada di tabel penduduk
            ],
            'jenis_surat' => 'required|string|max:100', // Jenis surat menentukan validasi lain
            'keperluan' => 'required|string|max:500',
            'tanggal_request' => 'sometimes|required|date', // Bisa otomatis diisi
            'attachment_bukti_pendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Contoh validasi file
        ];

        // --- Validasi Kondisional Berdasarkan Jenis Surat ---
        $jenisSurat = $request->input('jenis_surat');
        $conditionalRules = [];

        switch ($jenisSurat) {
            case 'SK_KEMATIAN':
                $conditionalRules = [
                    'nik_penduduk_meninggal' => ['required', 'string', 'digits:16', Rule::exists('penduduks', 'nik')],
                    'tanggal_kematian' => 'required|date',
                    'waktu_kematian' => 'required|date_format:H:i', // Format jam:menit
                    'tempat_kematian' => 'required|string|max:255',
                    'penyebab_kematian' => 'required|string|max:255',
                    'hubungan_pelapor_kematian' => 'required|string|max:100',
                ];
                break;
            case 'SK_PINDAH':
                $conditionalRules = [
                    'alamat_tujuan' => 'required|string|max:255',
                    'rt_tujuan' => 'required|string|max:5',
                    'rw_tujuan' => 'required|string|max:5',
                    'kelurahan_desa_tujuan' => 'required|string|max:100',
                    'kecamatan_tujuan' => 'required|string|max:100',
                    'kabupaten_kota_tujuan' => 'required|string|max:100',
                    'provinsi_tujuan' => 'required|string|max:100',
                    'alasan_pindah' => 'required|string|max:255',
                    'klasifikasi_pindah' => 'required|string|max:100',
                    'data_pengikut_pindah' => 'nullable|array', // Validasi array jika perlu lebih detail
                    'data_pengikut_pindah.*.nik' => 'sometimes|required|string|digits:16',
                    'data_pengikut_pindah.*.nama' => 'sometimes|required|string|max:255',
                    // tambahkan validasi lain untuk pengikut jika perlu
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
                     'nik_penduduk_ibu' => ['required', 'string', 'digits:16', Rule::exists('penduduks', 'nik')],
                     'nik_penduduk_ayah' => ['nullable', 'string', 'digits:16', Rule::exists('penduduks', 'nik')],
                     'nik_penduduk_pelapor_lahir' => ['required', 'string', 'digits:16', Rule::exists('penduduks', 'nik')],
                     'hubungan_pelapor_lahir' => 'required|string|max:100',
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
            case 'REKOM_KIS':
            case 'SKTM':
                 $conditionalRules = [
                    'penghasilan_perbulan_kepala_keluarga' => 'required|numeric|min:0',
                    'pekerjaan_kepala_keluarga' => 'required|string|max:100',
                    // Field KIP
                    'nik_penduduk_siswa' => ['required_if:jenis_surat,REKOM_KIP', 'nullable', 'string', 'digits:16', Rule::exists('penduduks', 'nik')],
                    'nama_sekolah' => 'required_if:jenis_surat,REKOM_KIP|nullable|string|max:255',
                    'nisn_siswa' => 'required_if:jenis_surat,REKOM_KIP|nullable|string|max:20',
                    'kelas_siswa' => 'required_if:jenis_surat,REKOM_KIP|nullable|string|max:20',
                 ];
                 break;
             case 'SK_KEHILANGAN_KTP':
                 $conditionalRules = [
                    'nomor_ktp_hilang' => 'required|string|digits:16',
                    'tanggal_perkiraan_hilang' => 'required|date',
                    'lokasi_perkiraan_hilang' => 'required|string|max:255',
                    'kronologi_singkat' => 'required|string|max:1000',
                    'nomor_laporan_polisi' => 'nullable|string|max:100',
                    'tanggal_laporan_polisi' => 'nullable|date|required_with:nomor_laporan_polisi',
                 ];
                 break;
             // Tambahkan case lain jika perlu
        }

        // Gabungkan rules dan validasi
        $validator = Validator::make($request->all(), array_merge($baseRules, $conditionalRules));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Unprocessable Entity
        }

        // Ambil data yang sudah divalidasi
        $validatedData = $validator->validated();

        // Set status awal
        $validatedData['status_surat'] = 'Pending'; // Status awal saat diajukan
        $validatedData['tanggal_request'] = $validatedData['tanggal_request'] ?? now(); // Isi tanggal request jika tidak ada
   
        // Nomor surat akan digenerate oleh Model saat event 'creating'
        // Handle file upload jika ada
        if ($request->hasFile('attachment_bukti_pendukung')) {
            // Simpan file dan dapatkan path-nya
            $path = $request->file('attachment_bukti_pendukung')->store('bukti_pendukung', 'public'); // Simpan di storage/app/public/bukti_pendukung
            $validatedData['attachment_bukti_pendukung'] = $path;
        }


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
                'pelaporKelahiran',
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
    public function update(Request $request, string $id)
    {
        $surat = Surat::findOrFail($id); // Cari surat atau 404

        // --- Tanpa Validasi Kompleks di Backend (Andalkan Frontend/Input Admin) ---
        // Ambil semua data dari request
        $inputData = $request->all();

        // Hapus field yang seharusnya tidak boleh diupdate dari input
        // (Penting untuk keamanan dan konsistensi!)
        unset(
            $inputData['id_surat'],     // Primary key tidak boleh diubah
            $inputData['nomor_surat'], // Nomor surat tidak boleh diubah manual
            $inputData['jenis_surat'], // Jenis surat sebaiknya tidak diubah
            $inputData['nik_pemohon'], // NIK pemohon sebaiknya tidak diubah di sini
            $inputData['created_at'],  // Timestamp otomatis
            $inputData['updated_at'],  // Timestamp otomatis
            $inputData['attachment_bukti_pendukung'] // <-- Tambahkan ini ke unset
        );
        $surat->fill($inputData);

        // Simpan perubahan ke database
        $surat->save();

        return response()->json([
            'message' => 'Data surat berhasil diperbarui',
            // Muat ulang data dari DB untuk memastikan konsistensi + load relasi
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
            'status_surat' => ['required', Rule::in(['Approved', 'Rejected'])], // Gunakan status dari model
            'catatan' => 'nullable|string|max:1000'
        ]);

        $surat = Surat::findOrFail($id); // findOrFail akan otomatis 404 jika tidak ketemu

        // Gunakan metode di model jika ada logika tambahan, jika tidak set langsung
        if ($request->status_surat === 'Approved') {
             // Cek apakah sudah ada nomor surat (seharusnya sudah dari creating)
             if (empty($surat->nomor_surat)) {
                 // Seharusnya tidak terjadi, tapi sebagai fallback jika proses creating gagal
                 // Log error atau handle kasus ini
                 // Mungkin generate ulang? (Hati-hati duplikasi nomor jika ada race condition)
                  return response()->json(['message' => 'Gagal menyetujui: Nomor surat belum tergenerate.'], 500);
             }
             $surat->status_surat = 'Approved';
             $surat->tanggal_approval = now()->toDateString(); // Set tanggal approval
        } else {
             $surat->status_surat = 'Rejected';
             $surat->tanggal_approval = null; // Hapus tanggal approval jika ditolak
        }

        // Set catatan internal
        if ($request->has('catatan_internal')) {
            $surat->catatan_internal = $request->catatan_internal;
        }

        $surat->save();
    

        return response()->json([
            'message' => 'Status surat berhasil diperbarui',
            'data' => $surat
        ]);
    }

    /**

     * Generate PDF untuk surat yang sudah disetujui.
     * (GET /surat/pdf/{id_surat})
     */
    public function generatePDF(string $id)
    {
        // Eager load relasi yang dibutuhkan di PDF
        $surat = Surat::with([
                    'pemohon',
                    'pendudukMeninggal',
                    'ibuBayi',
                    'ayahBayi',
                    'pelaporKelahiran',
                    'siswa'
                ])->findOrFail($id); // Otomatis 404 jika tidak ada

        // Periksa status surat (Gunakan status_surat)
        // Anda mungkin ingin menambahkan status 'Printed' sebagai status valid juga
        if ($surat->status_surat !== 'Approved' /* && $surat->status_surat !== 'Printed' */) {
           return response()->json(['message' => 'Surat belum disetujui atau tidak valid untuk diunduh'], 403); // Forbidden
        }

        // Tentukan view berdasarkan jenis surat (ini bagian penting)
        $viewName = 'pdf.templates.' . Str::lower(Str::snake($surat->jenis_surat)); // e.g., pdf.templates.sk_kematian

        // Cek apakah view ada, jika tidak gunakan view default/generic
        if (!view()->exists($viewName)) {
             // Log::warning("View PDF tidak ditemukan untuk jenis surat: {$surat->jenis_surat}. Menggunakan view default.");
             $viewName = 'pdf.surat_generic'; // Buat view generic jika perlu
             if (!view()->exists($viewName)) {
                  return response()->json(['message' => 'Template PDF tidak ditemukan.'], 500);
             }
        }

        // Load view spesifik dengan data surat
        $pdf = Pdf::loadView($viewName, compact('surat'));

        // Buat nama file yang deskriptif
        $filename = 'SURAT-'
                  . Str::upper(Str::slug($surat->jenis_surat, '_')) . '-'
                  . Str::slug($surat->pemohon->nama ?? $surat->nik_pemohon, '-') . '-' // Gunakan nama pemohon jika ada
                  . $surat->getKey() // Gunakan ID primary key
                  . '.pdf';

        // Tawarkan download
        return $pdf->download($filename);
        // Atau tampilkan di browser: return $pdf->stream($filename);
    }
}