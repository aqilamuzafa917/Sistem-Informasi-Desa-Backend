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
        return response()->json(Surat::all());
    }

    // 3. Admin menyetujui/reject surat
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $surat = Surat::findOrFail($id);
        $surat->update(['status' => $request->status]);

        return response()->json(['message' => 'Status surat diperbarui']);
    }

    // 4. Generate PDF surat
    public function generatePDF($id)
{
    $surat = Surat::findOrFail($id);

    if ($surat->status !== 'approved') {
        return response()->json(['message' => 'Surat belum disetujui'], 400);
    }

    // Pastikan nomor surat tidak mengandung karakter terlarang
    $nomorSuratFile = str_replace(['/', '\\'], '_', $surat->nomor_surat);

    $pdf = Pdf::loadView('pdf.surat', compact('surat'))->setPaper('a4', 'portrait');

    return $pdf->download('SURAT-' . $nomorSuratFile . '.pdf');
}

}
