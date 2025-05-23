<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;

class PendudukController extends Controller
{
    public function index()
    {
        $penduduk = Penduduk::all();
        return response()->json($penduduk);
    }

    public function searchByNik()
    {
        $query = trim(request()->input('query'));
        if (!$query) {
            return response()->json([]);
        }

        $penduduk = Penduduk::where('nik', 'LIKE', "%$query%")
            ->get();

        return response()->json($penduduk);
    }

    public function addPenduduk(Request $request)
    {
        $penduduk = Penduduk::create($request->all());
        return response()->json($penduduk, 201);
    }

    public function updatePenduduk(Request $request, $nik)
    {
        $penduduk = Penduduk::findOrFail($nik);
        $penduduk->update($request->all());
        return response()->json($penduduk);
    }

    public function deletePenduduk($nik)
    {
        $penduduk = Penduduk::findOrFail($nik);
        $penduduk->delete();
        return response()->json(null, 204);
    }

    public function getStatistikPenduduk()
    {
        $totalPenduduk = Penduduk::count();
        $totalLakiLaki = Penduduk::where('jenis_kelamin', \App\Enums\JenisKelamin::LakiLaki)->count();
        $totalPerempuan = Penduduk::where('jenis_kelamin', \App\Enums\JenisKelamin::Perempuan)->count();

        return response()->json([
            'total_penduduk' => $totalPenduduk,
            'total_laki_laki' => $totalLakiLaki,
            'total_perempuan' => $totalPerempuan,
        ]);
    }
}
