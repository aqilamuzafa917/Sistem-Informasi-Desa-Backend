<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Enums\JenisKelamin;
use App\Enums\StatusPerkawinan;
use App\Enums\Agama;
use Illuminate\Support\Facades\DB;
use Spatie\Async\Pool;
use Illuminate\Support\Facades\Log;
use Throwable;

class PendudukController extends Controller
{
    public function index()
    {
        $penduduk = Penduduk::all();
        return response()->json($penduduk);
    }

    public function searchByNik()
    {
        $query = trim(request()->input('query'));
        if (!$query) {
            return response()->json([]);
        }

        $penduduk = Penduduk::where('nik', 'LIKE', "%$query%")
            ->get();

        return response()->json($penduduk);
    }

    public function addPenduduk(Request $request)
    {
        $penduduk = Penduduk::create($request->all());
        return response()->json($penduduk, 201);
    }

    public function updatePenduduk(Request $request, $nik)
    {
        $penduduk = Penduduk::findOrFail($nik);
        $penduduk->update($request->all());
        return response()->json($penduduk);
    }

    public function deletePenduduk($nik)
    {
        $penduduk = Penduduk::findOrFail($nik);
        $penduduk->delete();
        return response()->json(null, 204);
    }

    public function getStatistikPenduduk()
    {
        $pool = Pool::create();

        // Task 1: Basic stats and age groups
        $basicAndAgeTask = $pool->add(function() {
            return Penduduk::selectRaw('
                -- Basic counts
                COUNT(*) as total_penduduk,
                COUNT(CASE WHEN jenis_kelamin = ? THEN 1 END) as total_laki_laki,
                COUNT(CASE WHEN jenis_kelamin = ? THEN 1 END) as total_perempuan,
                COUNT(DISTINCT no_kk) as total_kk,
                
                -- Age groups
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 0 AND 4 AND jenis_kelamin = ? THEN 1 END) as usia_0_4_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 0 AND 4 AND jenis_kelamin = ? THEN 1 END) as usia_0_4_perempuan,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 5 AND 9 AND jenis_kelamin = ? THEN 1 END) as usia_5_9_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 5 AND 9 AND jenis_kelamin = ? THEN 1 END) as usia_5_9_perempuan,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 10 AND 14 AND jenis_kelamin = ? THEN 1 END) as usia_10_14_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 10 AND 14 AND jenis_kelamin = ? THEN 1 END) as usia_10_14_perempuan,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 15 AND 19 AND jenis_kelamin = ? THEN 1 END) as usia_15_19_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 15 AND 19 AND jenis_kelamin = ? THEN 1 END) as usia_15_19_perempuan,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 20 AND 29 AND jenis_kelamin = ? THEN 1 END) as usia_20_29_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 20 AND 29 AND jenis_kelamin = ? THEN 1 END) as usia_20_29_perempuan,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 30 AND 39 AND jenis_kelamin = ? THEN 1 END) as usia_30_39_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 30 AND 39 AND jenis_kelamin = ? THEN 1 END) as usia_30_39_perempuan,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 40 AND 49 AND jenis_kelamin = ? THEN 1 END) as usia_40_49_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 40 AND 49 AND jenis_kelamin = ? THEN 1 END) as usia_40_49_perempuan,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 50 AND 59 AND jenis_kelamin = ? THEN 1 END) as usia_50_59_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 50 AND 59 AND jenis_kelamin = ? THEN 1 END) as usia_50_59_perempuan,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) >= 60 AND jenis_kelamin = ? THEN 1 END) as usia_60_plus_laki,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) >= 60 AND jenis_kelamin = ? THEN 1 END) as usia_60_plus_perempuan
            ', array_merge(
                // Basic counts (2 parameters)
                [JenisKelamin::LakiLaki->value, JenisKelamin::Perempuan->value],
                // Age groups (18 parameters - alternating Laki-laki and Perempuan)
                array_reduce(range(0, 8), function($carry, $i) {
                    return array_merge($carry, [JenisKelamin::LakiLaki->value, JenisKelamin::Perempuan->value]);
                }, [])
            ))->first();
        });

        // Task 2: Status perkawinan and agama
        $statusAndAgamaTask = $pool->add(function() {
            return Penduduk::selectRaw('
                -- Status perkawinan
                COUNT(CASE WHEN status_perkawinan = ? THEN 1 END) as belum_menikah,
                COUNT(CASE WHEN status_perkawinan = ? THEN 1 END) as menikah,
                COUNT(CASE WHEN status_perkawinan = ? THEN 1 END) as cerai_hidup,
                COUNT(CASE WHEN status_perkawinan = ? THEN 1 END) as cerai_mati,
                
                -- Agama
                COUNT(CASE WHEN agama = ? THEN 1 END) as islam,
                COUNT(CASE WHEN agama = ? THEN 1 END) as kristen,
                COUNT(CASE WHEN agama = ? THEN 1 END) as katolik,
                COUNT(CASE WHEN agama = ? THEN 1 END) as hindu,
                COUNT(CASE WHEN agama = ? THEN 1 END) as buddha,
                COUNT(CASE WHEN agama = ? THEN 1 END) as konghucu
            ', [
                StatusPerkawinan::BelumMenikah->value, StatusPerkawinan::Menikah->value,
                StatusPerkawinan::CeraiHidup->value, StatusPerkawinan::CeraiMati->value,
                Agama::Islam->value, Agama::Kristen->value, Agama::Katolik->value,
                Agama::Hindu->value, Agama::Buddha->value, Agama::Konghucu->value,
            ])->first();
        });

        // Task 3: Pendidikan
        $pendidikanTask = $pool->add(function() {
            return Penduduk::select('pendidikan', DB::raw('COUNT(*) as total'))
                ->whereNotNull('pendidikan')
                ->groupBy('pendidikan')
                ->pluck('total', 'pendidikan')
                ->toArray();
        });

        // Task 4: Pekerjaan
        $pekerjaanTask = $pool->add(function() {
            return Penduduk::select('pekerjaan', DB::raw('COUNT(*) as total'))
                ->whereNotNull('pekerjaan')
                ->groupBy('pekerjaan')
                ->pluck('total', 'pekerjaan')
                ->toArray();
        });

        // Wait for all tasks to complete
        $pool->wait();

        // Get results from each task
        $basicAndAge = $basicAndAgeTask->getOutput();
        $statusAndAgama = $statusAndAgamaTask->getOutput();
        $pendidikan = $pendidikanTask->getOutput();
        $pekerjaan = $pekerjaanTask->getOutput();

        // Structure the response data
        return response()->json([
            'total_penduduk' => $basicAndAge->total_penduduk,
            'total_laki_laki' => $basicAndAge->total_laki_laki,
            'total_perempuan' => $basicAndAge->total_perempuan,
            'total_kk' => $basicAndAge->total_kk,
            'data_usia' => [
                '0_4' => [
                    'laki_laki' => $basicAndAge->usia_0_4_laki,
                    'perempuan' => $basicAndAge->usia_0_4_perempuan,
                ],
                '5_9' => [
                    'laki_laki' => $basicAndAge->usia_5_9_laki,
                    'perempuan' => $basicAndAge->usia_5_9_perempuan,
                ],
                '10_14' => [
                    'laki_laki' => $basicAndAge->usia_10_14_laki,
                    'perempuan' => $basicAndAge->usia_10_14_perempuan,
                ],
                '15_19' => [
                    'laki_laki' => $basicAndAge->usia_15_19_laki,
                    'perempuan' => $basicAndAge->usia_15_19_perempuan,
                ],
                '20_29' => [
                    'laki_laki' => $basicAndAge->usia_20_29_laki,
                    'perempuan' => $basicAndAge->usia_20_29_perempuan,
                ],
                '30_39' => [
                    'laki_laki' => $basicAndAge->usia_30_39_laki,
                    'perempuan' => $basicAndAge->usia_30_39_perempuan,
                ],
                '40_49' => [
                    'laki_laki' => $basicAndAge->usia_40_49_laki,
                    'perempuan' => $basicAndAge->usia_40_49_perempuan,
                ],
                '50_59' => [
                    'laki_laki' => $basicAndAge->usia_50_59_laki,
                    'perempuan' => $basicAndAge->usia_50_59_perempuan,
                ],
                '60_plus' => [
                    'laki_laki' => $basicAndAge->usia_60_plus_laki,
                    'perempuan' => $basicAndAge->usia_60_plus_perempuan,
                ],
            ],
            'data_pendidikan' => $pendidikan,
            'data_pekerjaan' => $pekerjaan,
            'data_status_perkawinan' => [
                'belum_menikah' => $statusAndAgama->belum_menikah,
                'menikah' => $statusAndAgama->menikah,
                'cerai_hidup' => $statusAndAgama->cerai_hidup,
                'cerai_mati' => $statusAndAgama->cerai_mati,
            ],
            'data_agama' => [
                'islam' => $statusAndAgama->islam,
                'kristen' => $statusAndAgama->kristen,
                'katolik' => $statusAndAgama->katolik,
                'hindu' => $statusAndAgama->hindu,
                'buddha' => $statusAndAgama->buddha,
                'konghucu' => $statusAndAgama->konghucu,
            ],
        ]);
    }

    public function getStatistikPendudukForChatbot(Request $request)
    {
        try {
            $stats = Penduduk::selectRaw('
                COUNT(*) as total_penduduk,
                COUNT(CASE WHEN jenis_kelamin = ? THEN 1 END) as total_laki_laki,
                COUNT(CASE WHEN jenis_kelamin = ? THEN 1 END) as total_perempuan,
                COUNT(DISTINCT no_kk) as total_kk,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) < 17 THEN 1 END) as usia_anak,
                COUNT(CASE WHEN EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) >= 60 THEN 1 END) as usia_lansia
            ', [
                JenisKelamin::LakiLaki->value,
                JenisKelamin::Perempuan->value
            ])->first();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_penduduk' => $stats->total_penduduk,
                    'total_laki_laki' => $stats->total_laki_laki,
                    'total_perempuan' => $stats->total_perempuan,
                    'total_kk' => $stats->total_kk,
                    'usia_anak' => $stats->usia_anak,
                    'usia_lansia' => $stats->usia_lansia
                ]
            ]);

        } catch (Throwable $e) {
            Log::error('Error in getStatistikPendudukForChatbot: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan internal saat mengambil data statistik.'
            ], 500);
        }
    }

    public function searchByNoKK()
    {
        $query = trim(request()->input('query'));
        if (!$query) {
            return response()->json([]);
        }

        $penduduk = Penduduk::where('no_kk', 'LIKE', "%$query%")
            ->get();

        return response()->json($penduduk);
    }

    public function getNamaByNik($nik)
    {
        $penduduk = Penduduk::where('nik', $nik)
            ->select('nama')
            ->first();

        if (!$penduduk) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Split the name into parts and mask each part
        $namaParts = explode(' ', $penduduk->nama);
        $maskedNama = array_map(function($part) {
            return $part[0] . str_repeat('*', strlen($part) - 1);
        }, $namaParts);

        return response()->json([
            'nama' => implode(' ', $maskedNama)
        ]);
    }
}
