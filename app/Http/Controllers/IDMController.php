<?php

namespace App\Http\Controllers;

use App\Models\IDM;
use Illuminate\Http\Request;

class IDMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $idm = IDM::all();
        return response()->json($idm);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tahun' => 'required|integer',
            'skor_idm' => 'required|numeric',
            'status_idm' => 'required|string|max:255',
            'target_status' => 'required|string|max:255',
            'skor_minimal' => 'required|numeric',
            'penambahan' => 'required|numeric',
            'komponen' => 'nullable|json'
        ]);

        $idm = IDM::create($validatedData);
        return response()->json($idm, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, IDM $iDM)
    {
        if ($iDM) {
            return response()->json($iDM);
        }
        else {
            $iDM = IDM::where('tahun', $request->tahun)->first();
            if ($iDM) {
                return response()->json($iDM);
            } else {
                return response()->json(['message' => 'IDM not found'], 404);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IDM $iDM)
    {
        $validatedData = $request->validate([
            'tahun' => 'sometimes|required|integer',
            'skor_idm' => 'sometimes|required|numeric',
            'status_idm' => 'sometimes|required|string|max:255',
            'target_status' => 'sometimes|required|string|max:255',
            'skor_minimal' => 'sometimes|required|numeric',
            'penambahan' => 'sometimes|required|numeric',
            'komponen' => 'nullable|json'
        ]);

        $iDM->update($validatedData);
        return response()->json($iDM);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IDM $iDM)
    {
        $iDM->delete();
        return response()->json(null, 204);
    }
}
