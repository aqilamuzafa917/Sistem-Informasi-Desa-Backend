<?php

namespace App\Http\Controllers;

use App\Models\IndikatorIDM;
use Illuminate\Http\Request;

class IndikatorIDMController extends Controller
{
    public function index()
    {
        $indikator = IndikatorIDM::all();
        return $indikator;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'indikator' => 'required|string|max:255',
        ]);

        $indikator = IndikatorIDM::create($validatedData);
        return response()->json($indikator, 201);
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
