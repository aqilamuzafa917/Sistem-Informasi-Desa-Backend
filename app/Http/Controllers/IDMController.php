<?php

namespace App\Http\Controllers;

use App\Models\IDM;
use App\Models\VariabelIDM;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class IDMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $tahun)
    {
        $skors = $this->calculateScore($tahun);
        $skorIDM = $skors['skorIDM'];
        $statusList = [
            ['label' => 'Sangat Tertinggal', 'min' => 0.000],
            ['label' => 'Tertinggal',        'min' => 0.491],
            ['label' => 'Berkembang',        'min' => 0.599],
            ['label' => 'Maju',              'min' => 0.707],
            ['label' => 'Mandiri',           'min' => 0.815],
        ];
        $statusIDM = null;
        $targetStatus = null;
        $skorMinimal = null;

        foreach ($statusList as $index => $status) {
            $min = $status['min'];
            $max = $statusList[$index + 1]['min'] ?? 1.0;

            if ($skorIDM >= $min && $skorIDM < $max) {
                $statusIDM = $status['label'];

                $target = $statusList[$index + 1] ?? null;
                $targetStatus = $target['label'] ?? null;
                $skorMinimal = $target['min'] ?? null;
                break;
            }
        }
        $skors->forget('skorIDM');
        $iDMSebelum = IDM::where('tahun', $tahun - 1)->first() ?? 0;
        $iDM = IDM::create([
            'tahun' => $tahun,
            'skor_idm' => $skorIDM,
            'status_idm' => $statusIDM,
            'target_status' => $targetStatus,
            'skor_minimal' => $skorMinimal,
            'penambahan' => $skorIDM - $iDMSebelum->skor_idm ?? 0,
            'komponen' => $skors->toArray(),
        ]);

        return response()->json($iDM, 201, ['message' => 'Data IDM berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $tahun)
    {
        $iDM = IDM::where('tahun', $tahun)->first();
        if (!$iDM) {
            return response()->json(['message' => 'Data IDM tidak ditemukan'], 404);
        }

        $variabelIke = VariabelIDM::where('tahun', $tahun)->where('kategori', 'IKE')->get();
        $variabelIks = VariabelIDM::where('tahun', $tahun)->where('kategori', 'IKS')->get();
        $variabelIkl = VariabelIDM::where('tahun', $tahun)->where('kategori', 'IKL')->get();

        return response()->json([
            'idm' => $iDM,
            'variabel_ike' => $variabelIke,
            'variabel_iks' => $variabelIks,
            'variabel_ikl' => $variabelIkl,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IDM $iDM)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IDM $iDM)
    {
        $iDM->delete();
        return response()->json(null, 204);
    }

    public function casts() : array
    {
        return [
            'komponen' => AsArrayObject::class,
        ];
    }

    public function calculateScore(int $tahun)
    {
        $variabelIke = VariabelIDM::where('tahun', $tahun)->where('kategori', 'IKE')->get();
        $variabelIks = VariabelIDM::where('tahun', $tahun)->where('kategori', 'IKS')->get();
        $variabelIkl = VariabelIDM::where('tahun', $tahun)->where('kategori', 'IKL')->get();

        $totalSkorIke = $variabelIke->sum('skor');
        $totalSkorIks = $variabelIks->sum('skor');
        $totalSkorIkl = $variabelIkl->sum('skor');
        
        $skorIke = $totalSkorIke / (5 * $variabelIke->count());
        $skorIks = $totalSkorIks / (5 * $variabelIks->count());
        $skorIkl = $totalSkorIkl / (5 * $variabelIkl->count());
        $skorIdm = ($totalSkorIke + $totalSkorIks + $totalSkorIkl) / 3;

        return collect([
            'skorIKE' => $skorIke,
            'skorIKS' => $skorIks,
            'skorIKL' => $skorIkl,
            'skorIDM' => $skorIdm, 
        ]);
    }
}
