<?php

namespace App\Http\Controllers;

use App\Enums\KategoriVariabelIDM;
use App\Models\VariabelIDM;
use App\Models\IndikatorIDM;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VariabelIDMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show(int $tahun)
    {
        try {
            $variabelIDM = VariabelIDM::where('tahun', $tahun)
                ->orderBy('kategori')
                ->get()
                ->groupBy('kategori');

            if ($variabelIDM->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data variabel IDM yang tersedia',
                    'error' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'message' => 'Data variabel IDM berhasil diambil',
                'data' => [
                    'IKE' => $variabelIDM['IKE'] ?? [],
                    'IKS' => $variabelIDM['IKS'] ?? [],
                    'IKL' => $variabelIDM['IKL'] ?? []
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $daftarIndikator = IndikatorIDM::pluck('kategori', 'nama_indikator')->all();
            if (empty($daftarIndikator)) {
                return response()->json([
                    'message' => 'Tidak ada indikator IDM yang tersedia',
                    'error' => 'Indikator tidak ditemukan'
                ], 404);
            }
            return response()->json([
                'IKE' => collect($daftarIndikator)->filter(fn ($kategori) => $kategori === 'IKE')->keys()->values(),
                'IKS' => collect($daftarIndikator)->filter(fn ($kategori) => $kategori === 'IKS')->keys()->values(),
                'IKL' => collect($daftarIndikator)->filter(fn ($kategori) => $kategori === 'IKL')->keys()->values(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data indikator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $daftarIndikator = IndikatorIDM::pluck('nama_indikator')->toArray();
            if (empty($daftarIndikator)) {
                return response()->json([
                    'message' => 'Tidak ada indikator IDM yang tersedia',
                    'error' => 'Indikator tidak ditemukan'
                ], 404);
            }
            
            $request->validate([
                'data' => 'required|array',
                'data.*.indikator_idm' => ['required', 'string', Rule::in($daftarIndikator)],
                'data.*.skor' => 'required|integer|min:0|max:5',
                'data.*.keterangan' => 'nullable|string|max:255',
                'data.*.kegiatan' => 'nullable|string|max:255',
                'data.*.nilai_plus' => 'nullable|numeric|min:0',
                'data.*.pelaksana' => 'nullable|array',
                'data.*.kategori' => 'requ  ired|in:IKE,IKS,IKL',
                'data.*.tahun' => 'required|integer|max:'. date('Y'),
            ]);

            $variabelIDM = collect($request->data)->map(function ($item) {
                return [
                    'indikator_idm' => $item['indikator_idm'],
                    'skor' => $item['skor'],
                    'keterangan' => $item['keterangan'],
                    'kegiatan' => $item['kegiatan'],
                    'nilai_plus' => $item['nilai_plus'],
                    'pelaksana' => json_encode($item['pelaksana']),
                    'kategori' => $item['kategori'],
                    'tahun' => $item['tahun'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            $result = VariabelIDM::insert($variabelIDM);
            if (!$result) {
                throw new \Exception('Gagal menyimpan data variabel IDM');
            }

            return response()->json([
                'message' => 'Data IDM berhasil ditambahkan',
                'data' => $variabelIDM
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi data gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VariabelIDM $variabelIDM)
    {
        try {
            $daftarIndikator = IndikatorIDM::pluck('nama_indikator')->toArray();
            if (empty($daftarIndikator)) {
                return response()->json([
                    'message' => 'Tidak ada indikator IDM yang tersedia',
                    'error' => 'Indikator tidak ditemukan'
                ], 404);
            }

            if (!$variabelIDM) {
                return response()->json([
                    'message' => 'Data variabel IDM tidak ditemukan',
                    'error' => 'Not Found'
                ], 404);
            }

            return response()->json([
                'variabel_idm' => $variabelIDM,
                'indikator_idm' => $daftarIndikator,
                'kategori' => collect(KategoriVariabelIDM::cases())->map(fn ($kategori) => [
                    'value' => $kategori->value,
                    'label' => $kategori->label(),
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VariabelIDM $variabelIDM)
    {
        try {
            $daftarIndikator = IndikatorIDM::pluck('nama_indikator')->toArray();
            if (empty($daftarIndikator)) {
                return response()->json([
                    'message' => 'Tidak ada indikator IDM yang tersedia',
                    'error' => 'Indikator tidak ditemukan'
                ], 404);
            }

            if (!$variabelIDM) {
                return response()->json([
                    'message' => 'Data variabel IDM tidak ditemukan',
                    'error' => 'Not Found'
                ], 404);
            }

            $validatedData = $request->validate([
                'indikator_idm' => ['required', 'string', Rule::in($daftarIndikator)],
                'skor' => 'required|integer|min:0|max:5',
                'keterangan' => 'nullable|string|max:255',
                'kegiatan' => 'nullable|string|max:255',
                'nilai_plus' => 'nullable|numeric|min:0',
                'pelaksana' => 'nullable|array',
                'kategori' => 'required|in:IKE,IKS,IKL',
                'tahun' => 'required|integer|max:'. date('Y'),
            ]);

            $updated = $variabelIDM->update($validatedData);
            if (!$updated) {
                throw new \Exception('Gagal memperbarui data variabel IDM');
            }

            return response()->json([
                'message' => 'Data IDM berhasil diperbarui',
                'data' => $variabelIDM
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi data gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VariabelIDM $variabelIDM)
    {
        try {
            if (!$variabelIDM) {
                return response()->json([
                    'message' => 'Data variabel IDM tidak ditemukan',
                    'error' => 'Not Found'
                ], 404);
            }

            $deleted = $variabelIDM->delete();
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data variabel IDM');
            }

            return response()->json([
                'message' => 'Data IDM berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
