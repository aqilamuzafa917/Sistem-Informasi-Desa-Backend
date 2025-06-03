<?php

namespace App\Http\Controllers;

use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function getBoundary()
    {
        $polygon = ProfilDesa::firstOrFail()->batas_wilayah;

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
        $amenity = $request->input('amenity', 'school');

        $polygon = ProfilDesa::where('id', 1)->firstOrFail()->batas_wilayah;

        $bbox = '107.48,-6.93,107.53,-6.90';
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
        $poiInArea = [];

        foreach ($elements as $el) {
            $lat = $el['lat'] ?? ($el['center']['lat'] ?? null);
            $lon = $el['lon'] ?? ($el['center']['lon'] ?? null);

            if (!$lat || !$lon) continue;

            if ($this->pointInPolygon([$lon, $lat], $polygon)) {
                $poiInArea[] = [
                    'name' => $el['tags']['name'] ?? 'Tanpa Nama',
                    'lat' => $lat,
                    'lon' => $lon,
                    'tags' => $el['tags'] ?? [],
                ];
            }
        }

        return response()->json($poiInArea);
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
}
