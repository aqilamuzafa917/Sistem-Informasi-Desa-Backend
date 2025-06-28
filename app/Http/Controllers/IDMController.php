<?php

namespace App\Http\Controllers;

use App\Models\IDM;
use App\Models\VariabelIDM;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

use function Pest\Laravel\json;

class IDMController extends Controller
{
    private function generateIDM(int $tahun): IDM
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

        $iDMSebelum = IDM::where('tahun', $tahun - 1)->first();

        return IDM::create([
            'tahun' => $tahun,
            'skor_idm' => $skorIDM,
            'status_idm' => $statusIDM ?? '-',
            'target_status' => $targetStatus,
            'skor_minimal' => $skorMinimal,
            'penambahan' => $skorIDM - ($iDMSebelum->skor_idm ?? 0),
            'komponen' => $skors->toArray(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $tahun)
    {
        $iDM = IDM::where('tahun', $tahun)->first();

        if (!$iDM) {
            try {
                $iDM = $this->generateIDM($tahun);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Data IDM tidak tersedia dan gagal dihitung',
                    'error' => $e->getMessage()
                ], 500);
            }
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

    public function recalculate(Request $request, int $tahun)
    {
        try {
            // Hapus data IDM tahun tersebut jika sudah ada
            IDM::where('tahun', $tahun)->delete();

            // Hitung ulang & simpan
            $iDM = $this->generateIDM($tahun);

            return response()->json([
                'message' => 'Data IDM berhasil dihitung ulang',
                'data' => $iDM
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Kesalahan saat menyimpan data IDM',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghitung ulang IDM',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $skorIdm = ($skorIke + $skorIks + $skorIkl) / 3;

        return collect([
            'skorIKE' => $skorIke,
            'skorIKS' => $skorIks,
            'skorIKL' => $skorIkl,
            'skorIDM' => $skorIdm, 
        ]);
    }

    /**
     * Display a listing of all IDM data.
     */
    public function index()
    {
        $allIDM = IDM::orderBy('tahun', 'desc')->get();
        
        $result = [];
        foreach ($allIDM as $idm) {
            $variabelIke = VariabelIDM::where('tahun', $idm->tahun)->where('kategori', 'IKE')->get();
            $variabelIks = VariabelIDM::where('tahun', $idm->tahun)->where('kategori', 'IKS')->get();
            $variabelIkl = VariabelIDM::where('tahun', $idm->tahun)->where('kategori', 'IKL')->get();

            $result[] = [
                'idm' => $idm,
                'variabel_ike' => $variabelIke,
                'variabel_iks' => $variabelIks,
                'variabel_ikl' => $variabelIkl,
            ];
        }

        return response()->json($result);
    }

    /**
     * Display IDM statistics for all years.
     */
    public function stats()
    {
        $allIDM = IDM::orderBy('tahun', 'desc')->get();
        return response()->json($allIDM);
    }

    /**
     * Get IDM data for chatbot integration.
     * Supports both specific year requests and returns latest data if no year specified.
     */
    public function getDataForChatbot(Request $request)
    {
        try {
            $tahun = $request->input('tahun');
            
            // If no year specified, get the latest year
            if (!$tahun) {
                $latestIDM = IDM::orderBy('tahun', 'desc')->first();
                if (!$latestIDM) {
                    return response()->json([
                        'status' => 'not_found',
                        'message' => 'Data IDM tidak tersedia'
                    ], 404);
                }
                $tahun = $latestIDM->tahun;
            }

            // Get IDM data for the specified year
            $idm = IDM::where('tahun', $tahun)->first();
            
            if (!$idm) {
                // Try to generate IDM data if it doesn't exist
                try {
                    $idm = $this->generateIDM($tahun);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'not_found',
                        'message' => 'Data IDM untuk tahun ' . $tahun . ' tidak tersedia dan gagal dihitung',
                        'error' => $e->getMessage()
                    ], 404);
                }
            }

            // Format response for chatbot
            return response()->json([
                'status' => 'success',
                'data' => [
                    'tahun' => $idm->tahun,
                    'skor_idm' => number_format($idm->skor_idm, 3),
                    'status_idm' => $idm->status_idm,
                    'target_status' => $idm->target_status,
                    'skor_minimal_target' => $idm->skor_minimal ? number_format($idm->skor_minimal, 3) : null,
                    'komponen' => [
                        'skorIKS' => number_format($idm->komponen['skorIKS'] ?? 0, 3),
                        'skorIKE' => number_format($idm->komponen['skorIKE'] ?? 0, 3),
                        'skorIKL' => number_format($idm->komponen['skorIKL'] ?? 0, 3)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data IDM',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
