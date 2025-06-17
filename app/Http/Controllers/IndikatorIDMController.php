<?php

namespace App\Http\Controllers;

use App\Models\IndikatorIDM;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class IndikatorIDMController extends Controller
{
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'indikator' => 'required|array',
                'indikator.*.nama_indikator' => 'required|string|max:255',
                'indikator.*.kategori' => 'required|string|in:IKE,IKS,IKL',
            ]);

            $indikators = collect($validatedData['indikator'])->map(function ($item) {
                return IndikatorIDM::create([
                    'nama_indikator' => $item['nama_indikator'],
                    'kategori' => $item['kategori']
                ]);
            });

            return response()->json([
                'message' => 'Indikator berhasil ditambahkan',
                'data' => $indikators
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, IndikatorIDM $indikatorIDM)
    {
        try {
            $validatedData = $request->validate([
                'nama_indikator' => 'required|string|max:255',
                'kategori' => 'required|string|in:IKE,IKS,IKL',
            ]);

            $indikatorIDM->update($validatedData);

            return response()->json([
                'message' => 'Indikator berhasil diperbarui',
                'data' => $indikatorIDM
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui indikator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(IndikatorIDM $indikatorIDM)
    {
        try {
            $indikatorIDM->delete();

            return response()->json([
                'message' => 'Indikator berhasil dihapus'
            ], 204);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function batchUpdate(Request $request)
{
    try {
        $validatedData = $request->validate([
            'indikator' => 'required|array',
            'indikator.*.id' => 'required|exists:indikator_idm,id',
            'indikator.*.nama_indikator' => 'required|string|max:255',
            'indikator.*.kategori' => 'required|string|in:IKE,IKS,IKL',
        ]);

        $updated = collect($validatedData['indikator'])->map(function ($item) {
            $indikator = IndikatorIDM::find($item['id']);
            $indikator->update([
                'nama_indikator' => $item['nama_indikator'],
                'kategori' => $item['kategori'],
            ]);
            return $indikator;
        });

        return response()->json([
            'message' => 'Batch update berhasil',
            'data' => $updated
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat batch update',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
