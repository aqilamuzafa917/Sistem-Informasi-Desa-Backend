<?php

namespace App\Http\Controllers;

use App\Models\IndikatorIDM;
use Illuminate\Http\Request;

class IndikatorIDMController extends Controller
{
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'indikator' => 'required|array',
            'indikator.*' => 'required|string|max:255',
        ]);

        $indikators = collect($validatedData['indikator'])->map(function ($indikator) {
            return IndikatorIDM::create(['nama_indikator' => $indikator]);
        });

        return response()->json($indikators, 201);
    }

    public function update(Request $request, IndikatorIDM $indikatorIDM)
    {
        $validatedData = $request->validate([
            'indikator' => 'required|string|max:255',
        ]);

        $indikatorIDM->update($validatedData);
        return response()->json($indikatorIDM);
    }

    public function destroy(IndikatorIDM $indikatorIDM)
    {
        $indikatorIDM->delete();
        return response()->json(['message' => 'Indikator berhasil dihapus'], 204);
    }
}
