<?php

namespace App\Http\Controllers;

use App\Enums\KategoriVariabelIDM;
use App\Models\VariabelIDM;
use App\Models\IndikatorIDM;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VariabelIDMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $daftarIndikator = IndikatorIDM::pluck('indikator')->toArray();
        return response()->json([
            'indikator_idm' => $daftarIndikator,
            'kategori' => KategoriVariabelIDM::class,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $daftarIndikator = IndikatorIDM::pluck('indikator')->toArray();
        $validatedData = $request->validate([
            'indikator_idm' => ['required', 'string', Rule::in($daftarIndikator)],
            'skor' => 'required|integer',
            'keterangan' => 'nullable|string|max:255',
            'kegiatan' => 'nullable|string|max:255',
            'nilai_plus' => 'nullable|numeric',
            'pelaksana' => 'nullable|array',
            'kategori' => 'required|in:IKE,IKS,IKL',
            'tahun' => 'required|integer|max:'. date('Y'),
        ]);

        $variabelIDM = VariabelIDM::create($validatedData);
        return response()->json($variabelIDM, 201, ['message' => 'Data IDM berhasil ditambahkan']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VariabelIDM $variabelIDM)
    {
        $daftarIndikator = IndikatorIDM::pluck('indikator')->toArray();
        return response()->json([
            'variabel_idm' => $variabelIDM,
            'indikator_idm' => $daftarIndikator,
            'kategori' => KategoriVariabelIDM::class,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VariabelIDM $variabelIDM)
    {
        $daftarIndikator = IndikatorIDM::pluck('indikator')->toArray();
        $validatedData = $request->validate([
            'indikator_idm' => ['required', 'string', Rule::in($daftarIndikator)],
            'skor' => 'required|integer',
            'keterangan' => 'nullable|string|max:255',
            'kegiatan' => 'nullable|string|max:255',
            'nilai_plus' => 'nullable|numeric',
            'pelaksana' => 'nullable|array',
            'kategori' => 'required|in:IKE,IKS,IKL',
            'tahun' => 'required|integer|max:'. date('Y'),
        ]);

        $variabelIDM->update($validatedData);
        return response()->json($variabelIDM, 204, ['message' => 'Data IDM berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VariabelIDM $variabelIDM)
    {
        $variabelIDM->delete();
        return response()->json(['message' => 'Data IDM berhasil dihapus'], 204);
    }
}
