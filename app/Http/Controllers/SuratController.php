<?php
namespace App\Http\Controllers;

use App\Models\Surat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SuratController extends Controller
{
    // 1. Buat pengajuan surat
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'nik' => 'required|string',
            'jenis_surat' => 'required|string',
            'keperluan' => 'required|string',
        ]);

        $nomorSurat = Surat::generateNomorSurat();

        $surat = Surat::create([
            'nomor_surat' => $nomorSurat,
            'nama' => $request->nama,
            'nik' => $request->nik,
            'jenis_surat' => $request->jenis_surat,
            'keperluan' => $request->keperluan,
            'status' => 'pending', // Default status
        ]);

        return response()->json([
            'message' => 'Surat berhasil dibuat',
            'data' => $surat
        ], 201);
    }

    // 2. Lihat daftar surat (Admin)
    public function index()
    {
        // Sebaiknya tambahkan paginasi untuk performa
        // $surat = Surat::latest()->paginate(10);
        // return response()->json($surat);
        return response()->json(Surat::latest()->get()); // Ambil semua, urutkan terbaru
    }

    /**
     * Display the specified resource based on NIK.
     * (GET /surat/nik/{nik} - Public)
     */
    public function showByNik(string $nik)
    {
        // Validasi NIK (harus angka 16 digit)
        if (!ctype_digit($nik) || strlen($nik) !== 16) {
             return response()->json(['message' => 'Format NIK tidak valid. Harus 16 digit angka.'], 400);
        }

        $surat = Surat::where('nik', $nik)->latest()->get(); // Cari berdasarkan NIK, urutkan terbaru

        if ($surat->isEmpty()) {
            // Jika tidak ada surat ditemukan untuk NIK tersebut
            return response()->json(['message' => 'Tidak ada surat ditemukan untuk NIK ini'], 404);
        }

        // Kembalikan daftar surat yang ditemukan
        return response()->json($surat);
    }
    

    // 3. Admin menyetujui/reject surat
    public function update(Request $request, $id)
    {
        // Validasi input status
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan_admin' => 'nullable|string' // Tambahkan validasi untuk catatan jika ada
        ]);

        $surat = Surat::find($id); // Gunakan find() agar bisa handle not found
        if (!$surat) {
            return response()->json(['message' => 'Surat tidak ditemukan'], 404);
        }

        // Update status dan catatan admin jika ada
        $surat->status = $request->status;
        if ($request->has('catatan_admin')) {
            $surat->catatan_admin = $request->catatan_admin;
        }
        $surat->save();

        return response()->json([
            'message' => 'Status surat berhasil diperbarui',
            'data' => $surat // Kembalikan data surat yang sudah diupdate
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * (DELETE /surat/{id} - Requires Auth)
     */
    public function destroy(string $id)
    {
        $surat = Surat::find($id); // Cari surat berdasarkan ID
        if (!$surat) {
            // Jika surat tidak ditemukan
            return response()->json(['message' => 'Surat tidak ditemukan'], 404);
        }

        // Hapus surat dari database
        $surat->delete();

        // Kembalikan respons sukses
        return response()->json(['message' => 'Surat berhasil dihapus'], 200);
        // Alternatif: return response()->noContent(); // Status 204 jika tidak perlu body respons
    }

    // 4. Generate PDF surat
    /**
     * Generate PDF for the specified resource.
     * (GET /surat/pdf/{id} - Public, but requires 'approved' or 'disetujui' status)
     */
    public function generatePDF(string $id)
    {
        $surat = Surat::find($id);
        if (!$surat) {
            // Jika surat dengan ID tersebut tidak ada sama sekali
            return response()->json(['message' => 'Surat tidak ditemukan'], 404);
        }

        // Periksa apakah status surat sudah 'approved' atau 'disetujui'
        if ($surat->status !== 'approved' && $surat->status !== 'disetujui') {
           // Jika status bukan 'approved' dan bukan 'disetujui', kembalikan error Forbidden
           return response()->json(['message' => 'Surat belum disetujui atau tidak valid untuk diunduh'], 403);
        }

        // Jika surat ditemukan dan statusnya valid, lanjutkan membuat PDF
        $pdf = Pdf::loadView('pdf.surat', compact('surat')); // Pastikan view pdf.surat ada
        $filename = 'SURAT-' . Str::slug($surat->jenis_surat, '-') . '-' . $surat->id . '.pdf';

        return $pdf->download($filename);
        // Atau tampilkan di browser: return $pdf->stream($filename);
    }
}
