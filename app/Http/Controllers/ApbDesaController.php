<?php

namespace App\Http\Controllers;

use App\Models\RealisasiPendapatan;
use App\Models\RealisasiBelanja;
use App\Models\TotalApbDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApbDesaController extends Controller
{
    // ===== REALISASI PENDAPATAN =====
    
    /**
     * Menampilkan daftar realisasi pendapatan
     */
    public function indexPendapatan(Request $request)
    {
        $query = RealisasiPendapatan::with('user:id,name');
        
        // Filter berdasarkan tahun anggaran jika ada
        if ($request->has('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        $pendapatan = $query->orderBy('tanggal_realisasi', 'desc')->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $pendapatan
        ]);
    }
    
    /**
     * Menyimpan data realisasi pendapatan baru
     */
    public function storePendapatan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_anggaran' => 'required|digits:4|integer',
            'tanggal_realisasi' => 'required|date',
            'kategori' => 'required|in:Pendapatan Asli Desa,Pendapatan Transfer,Pendapatan Lain-lain',
            'sub_kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'sumber_dana' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $pendapatan = RealisasiPendapatan::create([
                'tahun_anggaran' => $request->tahun_anggaran,
                'tanggal_realisasi' => $request->tanggal_realisasi,
                'kategori' => $request->kategori,
                'sub_kategori' => $request->sub_kategori,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'sumber_dana' => $request->sumber_dana,
                'keterangan' => $request->keterangan,
                'user_id' => $request->user()->id,
            ]);
            
            // Update total APB Desa
            $this->updateTotalApbDesa($request->tahun_anggaran, $request);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data pendapatan berhasil disimpan',
                'data' => $pendapatan
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menampilkan detail realisasi pendapatan
     */
    public function showPendapatan($id)
    {
        $pendapatan = RealisasiPendapatan::with('user:id,name')->find($id);
        
        if (!$pendapatan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pendapatan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $pendapatan
        ]);
    }
    
    /**
     * Memperbarui data realisasi pendapatan
     */
    public function updatePendapatan(Request $request, $id)
    {
        $pendapatan = RealisasiPendapatan::find($id);
        
        if (!$pendapatan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pendapatan tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'tahun_anggaran' => 'required|digits:4|integer',
            'tanggal_realisasi' => 'required|date',
            'kategori' => 'required|in:Pendapatan Asli Desa,Pendapatan Transfer,Pendapatan Lain-lain',
            'sub_kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'sumber_dana' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $oldTahun = $pendapatan->tahun_anggaran;
            
            $pendapatan->update([
                'tahun_anggaran' => $request->tahun_anggaran,
                'tanggal_realisasi' => $request->tanggal_realisasi,
                'kategori' => $request->kategori,
                'sub_kategori' => $request->sub_kategori,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'sumber_dana' => $request->sumber_dana,
                'keterangan' => $request->keterangan,
                'user_id' => $request->user()->id,
            ]);
            
            // Update total APB Desa untuk tahun lama dan baru jika berbeda
            $this->updateTotalApbDesa($oldTahun, $request);
            if ($oldTahun != $request->tahun_anggaran) {
                $this->updateTotalApbDesa($request->tahun_anggaran, $request);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data pendapatan berhasil diperbarui',
                'data' => $pendapatan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menghapus data realisasi pendapatan
     */
    public function destroyPendapatan(Request $request, $id)
    {
        $pendapatan = RealisasiPendapatan::find($id);
        
        if (!$pendapatan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pendapatan tidak ditemukan'
            ], 404);
        }
        
        try {
            DB::beginTransaction();
            
            $tahun = $pendapatan->tahun_anggaran;
            $pendapatan->delete();
            
            // Update total APB Desa
            $this->updateTotalApbDesa($tahun, $request);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data pendapatan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ===== REALISASI BELANJA =====
    
    /**
     * Menampilkan daftar realisasi belanja
     */
    public function indexBelanja(Request $request)
    {
        $query = RealisasiBelanja::with('user:id,name');
        
        // Filter berdasarkan tahun anggaran jika ada
        if ($request->has('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        $belanja = $query->orderBy('tanggal_realisasi', 'desc')->paginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $belanja
        ]);
    }
    
    /**
     * Menyimpan data realisasi belanja baru
     */
    public function storeBelanja(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_anggaran' => 'required|digits:4|integer',
            'tanggal_realisasi' => 'required|date',
            'kategori' => 'required|in:Belanja Barang/Jasa,Belanja Modal,Belanja Tak Terduga',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'penerima_vendor' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $belanja = RealisasiBelanja::create([
                'tahun_anggaran' => $request->tahun_anggaran,
                'tanggal_realisasi' => $request->tanggal_realisasi,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'penerima_vendor' => $request->penerima_vendor,
                'keterangan' => $request->keterangan,
                'user_id' => $request->user()->id,
            ]);
            
            // Update total APB Desa
            $this->updateTotalApbDesa($request->tahun_anggaran, $request);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data belanja berhasil disimpan',
                'data' => $belanja
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menampilkan detail realisasi belanja
     */
    public function showBelanja($id)
    {
        $belanja = RealisasiBelanja::with('user:id,name')->find($id);
        
        if (!$belanja) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data belanja tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $belanja
        ]);
    }
    
    /**
     * Memperbarui data realisasi belanja
     */
    public function updateBelanja(Request $request, $id)
    {
        $belanja = RealisasiBelanja::find($id);
        
        if (!$belanja) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data belanja tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'tahun_anggaran' => 'required|digits:4|integer',
            'tanggal_realisasi' => 'required|date',
            'kategori' => 'required|in:Belanja Barang/Jasa,Belanja Modal,Belanja Tak Terduga',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'penerima_vendor' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $oldTahun = $belanja->tahun_anggaran;
            
            $belanja->update([
                'tahun_anggaran' => $request->tahun_anggaran,
                'tanggal_realisasi' => $request->tanggal_realisasi,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'penerima_vendor' => $request->penerima_vendor,
                'keterangan' => $request->keterangan,
                'user_id' => $request->user()->id,
            ]);
            
            // Update total APB Desa untuk tahun lama dan baru jika berbeda
            $this->updateTotalApbDesa($oldTahun, $request);
            if ($oldTahun != $request->tahun_anggaran) {
                $this->updateTotalApbDesa($request->tahun_anggaran, $request);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data belanja berhasil diperbarui',
                'data' => $belanja
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menghapus data realisasi belanja
     */
    public function destroyBelanja(Request $request, $id)
    {
        $belanja = RealisasiBelanja::find($id);
        
        if (!$belanja) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data belanja tidak ditemukan'
            ], 404);
        }
        
        try {
            DB::beginTransaction();
            
            $tahun = $belanja->tahun_anggaran;
            $belanja->delete();
            
            // Update total APB Desa
            $this->updateTotalApbDesa($tahun, $request);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data belanja berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ===== TOTAL APB DESA =====
    
    /**
     * Menampilkan daftar total APB Desa
     */
    public function indexTotalApb(Request $request)
    {
        $query = TotalApbDesa::with('user:id,name');
        
        // Filter berdasarkan tahun anggaran jika ada
        if ($request->has('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }
        
        $totalApb = $query->orderBy('tahun_anggaran', 'desc')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $totalApb
        ]);
    }
    
    /**
     * Memperbarui atau membuat data total APB Desa berdasarkan tahun
     */
    protected function updateTotalApbDesa($tahun, Request $request)
    {
        // Hitung total pendapatan
        $totalPendapatan = RealisasiPendapatan::where('tahun_anggaran', $tahun)
            ->sum('jumlah');
        
        // Hitung total belanja
        $totalBelanja = RealisasiBelanja::where('tahun_anggaran', $tahun)
            ->sum('jumlah');
        
        // Hitung saldo/sisa
        $saldoSisa = $totalPendapatan - $totalBelanja;
        
        // Update atau buat data total APB Desa
        TotalApbDesa::updateOrCreate(
            ['tahun_anggaran' => $tahun],
            [
                'total_pendapatan' => $totalPendapatan,
                'total_belanja' => $totalBelanja,
                'saldo_sisa' => $saldoSisa,
                'tanggal_pelaporan' => now(),
                'user_id' => $request->user()->id,
            ]
        );
    }
    
    // ===== LAPORAN APB DESA =====
    
    /**
     * Mendapatkan laporan APB Desa berdasarkan tahun
     */
    public function getLaporanApbDesa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'nullable|digits:4|integer',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $tahun = $request->tahun ?? date('Y');
        
        // Ambil data total APB Desa
        $totalApb = TotalApbDesa::where('tahun_anggaran', $tahun)->first();
        
        if (!$totalApb) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data APB Desa untuk tahun ' . $tahun . ' tidak ditemukan'
            ], 404);
        }
        
        // Ambil detail pendapatan berdasarkan kategori
        $pendapatanByKategori = RealisasiPendapatan::where('tahun_anggaran', $tahun)
            ->select('kategori', DB::raw('SUM(jumlah) as total'))
            ->groupBy('kategori')
            ->get();
        
        // Ambil detail belanja berdasarkan kategori
        $belanjaByKategori = RealisasiBelanja::where('tahun_anggaran', $tahun)
            ->select('kategori', DB::raw('SUM(jumlah) as total'))
            ->groupBy('kategori')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'tahun_anggaran' => $tahun,
                'total_pendapatan' => $totalApb->total_pendapatan,
                'total_belanja' => $totalApb->total_belanja,
                'saldo_sisa' => $totalApb->saldo_sisa,
                'tanggal_pelaporan' => $totalApb->tanggal_pelaporan,
                'detail_pendapatan' => $pendapatanByKategori,
                'detail_belanja' => $belanjaByKategori
            ]
        ]);
    }
    
    /**
     * Mendapatkan statistik APB Desa untuk 5 tahun terakhir
     */
    public function getStatistikApbDesa()
    {
        // Ambil data total APB Desa untuk 5 tahun terakhir
        $tahunSekarang = date('Y');
        $tahunMulai = $tahunSekarang - 4;
        
        $dataApb = TotalApbDesa::whereBetween('tahun_anggaran', [$tahunMulai, $tahunSekarang])
            ->orderBy('tahun_anggaran')
            ->get();
        
        // Format data untuk chart
        $labels = [];
        $dataPendapatan = [];
        $dataBelanja = [];
        $dataSaldo = [];
        
        foreach ($dataApb as $apb) {
            $labels[] = $apb->tahun_anggaran;
            $dataPendapatan[] = $apb->total_pendapatan;
            $dataBelanja[] = $apb->total_belanja;
            $dataSaldo[] = $apb->saldo_sisa;
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Total Pendapatan',
                        'data' => $dataPendapatan
                    ],
                    [
                        'label' => 'Total Belanja',
                        'data' => $dataBelanja
                    ],
                    [
                        'label' => 'Saldo/Sisa',
                        'data' => $dataSaldo
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Mendapatkan laporan APB Desa untuk beberapa tahun
     */
    public function getLaporanMultiTahun(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_awal' => 'nullable|digits:4|integer',
            'tahun_akhir' => 'nullable|digits:4|integer|gte:tahun_awal',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $query = TotalApbDesa::query();
        
        // Filter berdasarkan rentang tahun jika ada
        if ($request->filled('tahun_awal') && $request->filled('tahun_akhir')) {
            $query->whereBetween('tahun_anggaran', [$request->tahun_awal, $request->tahun_akhir]);
        }
        
        $dataApb = $query->orderBy('tahun_anggaran')->get();
        
        if ($dataApb->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data APB Desa tidak ditemukan'
            ], 404);
        }
        
        // Siapkan data untuk respons
        $hasilLaporan = [];
        
        foreach ($dataApb as $apb) {
            // Ambil detail pendapatan berdasarkan kategori untuk tahun ini
            $pendapatanByKategori = RealisasiPendapatan::where('tahun_anggaran', $apb->tahun_anggaran)
                ->select('kategori', DB::raw('SUM(jumlah) as total'))
                ->groupBy('kategori')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->kategori => $item->total];
                });
            
            // Ambil detail belanja berdasarkan kategori untuk tahun ini
            $belanjaByKategori = RealisasiBelanja::where('tahun_anggaran', $apb->tahun_anggaran)
                ->select('kategori', DB::raw('SUM(jumlah) as total'))
                ->groupBy('kategori')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->kategori => $item->total];
                });
            
            $hasilLaporan[] = [
                'tahun_anggaran' => $apb->tahun_anggaran,
                'total_pendapatan' => $apb->total_pendapatan,
                'total_belanja' => $apb->total_belanja,
                'saldo_sisa' => $apb->saldo_sisa,
                'detail_pendapatan' => [
                    'Pendapatan Asli Desa' => $pendapatanByKategori['Pendapatan Asli Desa'] ?? 0,
                    'Pendapatan Transfer' => $pendapatanByKategori['Pendapatan Transfer'] ?? 0,
                    'Pendapatan Lain-lain' => $pendapatanByKategori['Pendapatan Lain-lain'] ?? 0
                ],
                'detail_belanja' => [
                    'Belanja Barang/Jasa' => $belanjaByKategori['Belanja Barang/Jasa'] ?? 0,
                    'Belanja Modal' => $belanjaByKategori['Belanja Modal'] ?? 0,
                    'Belanja Tak Terduga' => $belanjaByKategori['Belanja Tak Terduga'] ?? 0
                ]
            ];
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $hasilLaporan
        ]);
    }
}