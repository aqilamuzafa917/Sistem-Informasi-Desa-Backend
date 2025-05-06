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
}
