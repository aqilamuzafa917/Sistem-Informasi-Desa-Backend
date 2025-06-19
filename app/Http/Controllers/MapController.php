<?php

namespace App\Http\Controllers;

use App\Enums\KategoriPotensi;
use App\Models\PotensiLoc;
use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class MapController extends Controller
{

public function store(Request $request)
{
    try {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'kategori' => [
                'required',
                'string',
                Rule::in(array_column(KategoriPotensi::cases(), 'value')),
            ],
            'tags' => 'nullable|array',
        ]);

        $potensi = PotensiLoc::create([
            'nama' => $request->nama,
            'latitude' => $request->lat,
            'longitude' => $request->lon,
            'kategori' => $request->kategori,
            'tags' => $request->tags ?? [],
        ]);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$potensi->longitude, $potensi->latitude],
                ],
                'properties' => [
                    'name' => $potensi->nama,
                    'kategori' => $potensi->kategori,
                    'tags' => $potensi->tags,
                ],
            ]
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors' => $e->errors(),
        ], 422);
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'Kesalahan database',
            'error' => $e->getMessage(),
        ], 500);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat menyimpan potensi',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function index()
{
    $potensi = PotensiLoc::all();

    if ($potensi->isEmpty()) {
        return response()->json(['message' => 'Tidak ada potensi ditemukan'], 404);
    }

    $grouped = KategoriPotensi::cases();

    $featuresByKategori = collect($grouped)->mapWithKeys(function ($kategoriEnum) use ($potensi) {
        $filtered = $potensi->where('kategori', $kategoriEnum->value);

        return [$kategoriEnum->value => [
            'label' => $kategoriEnum->label(),
            'type' => 'FeatureCollection',
            'features' => $filtered->map(function ($item) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$item->longitude, $item->latitude],
                    ],
                    'properties' => [
                        'name' => $item->nama,
                        'kategori' => $item->kategori,
                        'tags' => $item->tags,
                    ],
                ];
            })->values(),
        ]];
    });

    return response()->json($featuresByKategori);
}


    public function show($id)
    {
        $potensi = PotensiLoc::findOrFail($id);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$potensi->longitude, $potensi->latitude],
                ],
                'properties' => [
                    'name' => $potensi->nama,
                    'kategori' => $potensi->kategori,
                    'tags' => $potensi->tags,
                ],
            ]
        ]);
    }

    public function update(Request $request, PotensiLoc $potensi)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
                'kategori' => [
                    'required',
                    'string',
                    Rule::in(array_column(KategoriPotensi::cases(), 'value')),
                ],
                'tags' => 'nullable|array',
            ]);

            $potensi->update([
                'nama' => $request->nama,
                'latitude' => $request->lat,
                'longitude' => $request->lon,
                'kategori' => $request->kategori,
                'tags' => $request->tags ?? [],
            ]);

            return response()->json([
                'message' => 'Data potensi berhasil diperbarui',
                'data' => $potensi
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Kesalahan database saat memperbarui data',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(PotensiLoc $potensi)
    {
        try {
            $potensi->delete();

            return response()->json([
                'message' => 'Data potensi berhasil dihapus'
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus data potensi (kesalahan database)',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data potensi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getBoundary()
    {
        $polygon = ProfilDesa::firstOrFail()->polygon_desa;

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [$polygon]
                ],
                'properties' => [
                    'name' => 'Desa Batujajar Timur', 
                ]
            ]
        ]);
    }

    public function getPOI(Request $request)
    {
        $amenity = $request->query('amenity', 'school');

        $polygon = ProfilDesa::where('id', 1)->firstOrFail()->polygon_desa;

        $bbox = '-6.93,107.48,-6.90,107.53';

        $query = <<<OVERPASS
            [out:json][timeout:25];
            (
            node["amenity"="$amenity"]($bbox);
            way["amenity"="$amenity"]($bbox);
            relation["amenity"="$amenity"]($bbox);
            );
            out center;
        OVERPASS;

        $response = Http::timeout(20)->asForm()->post('https://overpass-api.de/api/interpreter', [
            'data' => $query
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Gagal mengambil POI'], 500);
        }

        $elements = $response->json('elements');
        $filtered = collect($elements)
            ->map(function ($el) {
                $lat = $el['lat'] ?? ($el['center']['lat'] ?? null);
                $lon = $el['lon'] ?? ($el['center']['lon'] ?? null);

                return [
                    'name' => $el['tags']['name'] ?? 'Tanpa Nama',
                    'lat' => $lat,
                    'lon' => $lon,
                    'tags' => $el['tags'] ?? [],
                ];
            })
            ->filter(function ($el) use ($polygon) {
                return $el['lat'] && $el['lon'] && $this->pointInPolygon([$el['lon'], $el['lat']], $polygon);
            })
            ->unique('name'); 

        $features = $filtered->map(function ($el) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$el['lon'], $el['lat']],
                ],
                'properties' => [
                    'name' => $el['name'],
                    'tags' => $el['tags'],
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features->values(),
        ]);
    }


    private function pointInPolygon($point, $polygon)
    {
        $x = $point[0];
        $y = $point[1];
        $inside = false;
        $n = count($polygon);

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i][0]; $yi = $polygon[$i][1];
            $xj = $polygon[$j][0]; $yj = $polygon[$j][1];

            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-10) + $xi);

            if ($intersect) $inside = !$inside;
        }

        return $inside;
    }

    protected $casts = [
        'tags' => 'array',
    ];
}
