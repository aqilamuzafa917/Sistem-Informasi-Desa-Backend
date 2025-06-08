<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Enums\JenisKelamin;
use App\Enums\StatusPerkawinan;
use App\Enums\Agama;
use Illuminate\Support\Facades\DB;

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
        $totalPenduduk = Penduduk::count();
        $totalLakiLaki = Penduduk::where('jenis_kelamin', JenisKelamin::LakiLaki)->count();
        $totalPerempuan = Penduduk::where('jenis_kelamin', JenisKelamin::Perempuan)->count();
        $totalKK = Penduduk::distinct('no_kk')->count('no_kk');

        // Data Usia - Menggunakan kelompok usia standar demografi
        $usia = [
            '0_4' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 0 AND 4')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 0 AND 4')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
            '5_9' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 5 AND 9')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 5 AND 9')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
            '10_14' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 10 AND 14')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 10 AND 14')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
            '15_19' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 15 AND 19')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 15 AND 19')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
            '20_29' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 20 AND 29')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 20 AND 29')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
            '30_39' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 30 AND 39')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 30 AND 39')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
            '40_49' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 40 AND 49')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 40 AND 49')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
            '50_59' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 50 AND 59')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) BETWEEN 50 AND 59')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
            '60_plus' => [
                'laki_laki' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) >= 60')
                    ->where('jenis_kelamin', JenisKelamin::LakiLaki)->count(),
                'perempuan' => Penduduk::whereRaw('EXTRACT(YEAR FROM AGE(CURRENT_DATE, tanggal_lahir)) >= 60')
                    ->where('jenis_kelamin', JenisKelamin::Perempuan)->count(),
            ],
        ];

        // Data Pendidikan - Dinamis berdasarkan data yang ada
        $pendidikan = Penduduk::select('pendidikan', DB::raw('count(*) as total'))
            ->whereNotNull('pendidikan')
            ->groupBy('pendidikan')
            ->get()
            ->pluck('total', 'pendidikan')
            ->toArray();

        // Data Pekerjaan - Dinamis berdasarkan data yang ada
        $pekerjaan = Penduduk::select('pekerjaan', DB::raw('count(*) as total'))
            ->whereNotNull('pekerjaan')
            ->groupBy('pekerjaan')
            ->get()
            ->pluck('total', 'pekerjaan')
            ->toArray();

        // Data Status Perkawinan - Menggunakan enum values
        $status_perkawinan = [
            'belum_menikah' => Penduduk::where('status_perkawinan', StatusPerkawinan::BelumMenikah)->count(),
            'menikah' => Penduduk::where('status_perkawinan', StatusPerkawinan::Menikah)->count(),
            'cerai_hidup' => Penduduk::where('status_perkawinan', StatusPerkawinan::CeraiHidup)->count(),
            'cerai_mati' => Penduduk::where('status_perkawinan', StatusPerkawinan::CeraiMati)->count(),
        ];

        // Data Agama - Menggunakan enum values
        $agama = [
            'islam' => Penduduk::where('agama', Agama::Islam)->count(),
            'kristen' => Penduduk::where('agama', Agama::Kristen)->count(),
            'katolik' => Penduduk::where('agama', Agama::Katolik)->count(),
            'hindu' => Penduduk::where('agama', Agama::Hindu)->count(),
            'buddha' => Penduduk::where('agama', Agama::Buddha)->count(),
            'konghucu' => Penduduk::where('agama', Agama::Konghucu)->count(),
        ];

        return response()->json([
            'total_penduduk' => $totalPenduduk,
            'total_laki_laki' => $totalLakiLaki,
            'total_perempuan' => $totalPerempuan,
            'total_kk' => $totalKK,
            'data_usia' => $usia,
            'data_pendidikan' => $pendidikan,
            'data_pekerjaan' => $pekerjaan,
            'data_status_perkawinan' => $status_perkawinan,
            'data_agama' => $agama,
        ]);
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
}
