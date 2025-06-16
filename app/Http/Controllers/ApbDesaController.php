<?php

namespace App\Http\Controllers;

use App\Models\RealisasiPendapatan;
use App\Models\RealisasiBelanja;
use App\Models\TotalApbDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan ini
use Illuminate\Support\Facades\Log; // Tambahkan ini

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
            'pendapatan.*.tahun_anggaran' => 'required|digits:4|integer',
            'pendapatan.*.tanggal_realisasi' => 'required|date',
            'pendapatan.*.kategori' => 'required|in:Pendapatan Asli Desa,Pendapatan Transfer,Pendapatan Lain-lain',
            'pendapatan.*.sub_kategori' => 'required|string|max:255',
            'pendapatan.*.deskripsi' => 'required|string',
            'pendapatan.*.jumlah' => 'required|numeric|min:0',
            'pendapatan.*.sumber_dana' => 'required|string|max:255',
            'pendapatan.*.keterangan' => 'nullable|string',
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
            
            $createdPendapatan = [];
            $tahunAnggaranSet = [];

            foreach ($request->pendapatan as $item) {
                $pendapatan = RealisasiPendapatan::create([
                    'tahun_anggaran' => $item['tahun_anggaran'],
                    'tanggal_realisasi' => $item['tanggal_realisasi'],
                    'kategori' => $item['kategori'],
                    'sub_kategori' => $item['sub_kategori'],
                    'deskripsi' => $item['deskripsi'],
                    'jumlah' => $item['jumlah'],
                    'sumber_dana' => $item['sumber_dana'],
                    'keterangan' => $item['keterangan'] ?? null,
                    'user_id' => $request->user()->id,
                ]);
                $createdPendapatan[] = $pendapatan;
                $tahunAnggaranSet[$item['tahun_anggaran']] = true; // Keep track of unique years
            }
            
            // Update total APB Desa for each affected year
            foreach (array_keys($tahunAnggaranSet) as $tahun) {
                $this->updateTotalApbDesa($tahun, $request);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data pendapatan berhasil disimpan',
                'data' => $createdPendapatan
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
            'belanja.*.tahun_anggaran' => 'required|digits:4|integer',
            'belanja.*.tanggal_realisasi' => 'required|date',
            'belanja.*.kategori' => 'required|in:Belanja Barang/Jasa,Belanja Modal,Belanja Tak Terduga',
            'belanja.*.deskripsi' => 'required|string',
            'belanja.*.jumlah' => 'required|numeric|min:0',
            'belanja.*.penerima_vendor' => 'required|string|max:255',
            'belanja.*.keterangan' => 'nullable|string',
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
            
            $createdBelanja = [];
            $tahunAnggaranSet = [];

            foreach ($request->belanja as $item) {
                $belanja = RealisasiBelanja::create([
                    'tahun_anggaran' => $item['tahun_anggaran'],
                    'tanggal_realisasi' => $item['tanggal_realisasi'],
                    'kategori' => $item['kategori'],
                    'deskripsi' => $item['deskripsi'],
                    'jumlah' => $item['jumlah'],
                    'penerima_vendor' => $item['penerima_vendor'],
                    'keterangan' => $item['keterangan'] ?? null,
                    'user_id' => $request->user()->id,
                ]);
                $createdBelanja[] = $belanja;
                $tahunAnggaranSet[$item['tahun_anggaran']] = true; // Keep track of unique years
            }
            
            // Update total APB Desa for each affected year
            foreach (array_keys($tahunAnggaranSet) as $tahun) {
                $this->updateTotalApbDesa($tahun, $request);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data belanja berhasil disimpan',
                'data' => $createdBelanja
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
    public function updateTotalApbDesa($tahun, Request $request)
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
    public function getLaporanApbDesaForChatbot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|digits:4|integer', // Jadikan 'required' di sini juga
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal: Tahun wajib diisi.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Sekarang kita yakin $request->tahun pasti ada
        $tahun = (int) $request->tahun;
        
        $totalApb = TotalApbDesa::where('tahun_anggaran', $tahun)->first();
        
        if (!$totalApb) {
            return response()->json([
                'status' => 'not_found', // Gunakan status 'not_found' agar chatbot bisa menangani
                'message' => 'Data APB Desa untuk tahun ' . $tahun . ' tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'tahun_anggaran' => (int) $tahun,
                'total_pendapatan' => $totalApb->total_pendapatan,
                'total_belanja' => $totalApb->total_belanja,
                'saldo_sisa' => $totalApb->saldo_sisa,
                'tanggal_pelaporan' => $totalApb->tanggal_pelaporan,
            ]
        ]);
    }

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
    

    /**
     * Generate PDF laporan APB Desa berdasarkan tahun
     * (GET /apb-desa/pdf/{tahun})
     */
    public function generatePDF(Request $request, $tahun = null)
    {
        try {
            // Jika tahun tidak disediakan, gunakan tahun saat ini
            if (!$tahun) {
                $tahun = $request->tahun ?? date('Y');
            }

            // Validasi tahun
            if (!ctype_digit($tahun) || strlen($tahun) !== 4) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format tahun tidak valid. Harus 4 digit angka.'
                ], 400);
            }

            // Ambil data total APB Desa
            $totalApb = TotalApbDesa::where('tahun_anggaran', $tahun)->first();

            if (!$totalApb) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data APB Desa untuk tahun ' . $tahun . ' tidak ditemukan'
                ], 404);
            }

            // Ambil detail pendapatan berdasarkan kategori
            $pendapatanAsliDesa = RealisasiPendapatan::where('tahun_anggaran', $tahun)
                ->where('kategori', 'Pendapatan Asli Desa')
                ->select('sub_kategori as uraian', 'jumlah', 'keterangan') // Menambahkan keterangan jika ada
                ->get()
                ->toArray();

            $pendapatanTransfer = RealisasiPendapatan::where('tahun_anggaran', $tahun)
                ->where('kategori', 'Pendapatan Transfer')
                ->select('sub_kategori as uraian', 'jumlah', 'keterangan') // Menambahkan keterangan jika ada
                ->get()
                ->toArray();

            $pendapatanLain = RealisasiPendapatan::where('tahun_anggaran', $tahun)
                ->where('kategori', 'Pendapatan Lain-lain')
                ->select('sub_kategori as uraian', 'jumlah', 'keterangan') // Menambahkan keterangan jika ada
                ->get()
                ->toArray();

            // Hitung total per kategori pendapatan
            $totalPendapatanAsliDesa = array_sum(array_column($pendapatanAsliDesa, 'jumlah'));
            $totalPendapatanTransfer = array_sum(array_column($pendapatanTransfer, 'jumlah'));
            $totalPendapatanLain = array_sum(array_column($pendapatanLain, 'jumlah'));

            // Ambil detail belanja
            // Menggunakan struktur yang lebih detail untuk belanja jika diperlukan
            // Untuk saat ini, kita akan gunakan struktur yang sudah ada dan pastikan $this->getStrukturBelanjaData($tahun) diimplementasikan dengan benar
            $strukturBelanjaData = $this->getStrukturBelanjaData($tahun); // Anda perlu memastikan implementasi fungsi ini

            // Hitung surplus/defisit
            $surplusDefisit = $totalApb->total_pendapatan - $totalApb->total_belanja;

            // Data untuk view
            $data = [
                'tahun' => $tahun,
                'nama_desa' => optional(\App\Models\ProfilDesa::first())->nama_desa, // Ambil nama desa dari ProfilDesa
                'total_pendapatan' => $totalApb->total_pendapatan,
                'total_belanja' => $totalApb->total_belanja,
                'surplus_defisit' => $surplusDefisit,
                // 'sisa_anggaran' => $sisaAnggaran, // Dihapus
                
                'total_pendapatan_asli_desa' => $totalPendapatanAsliDesa,
                'total_pendapatan_transfer' => $totalPendapatanTransfer,
                'total_pendapatan_lain' => $totalPendapatanLain,
                // 'total_penerimaan_pembiayaan' => $totalPenerimaanPembiayaan, // Dihapus
                // 'total_pengeluaran_pembiayaan' => $totalPengeluaranPembiayaan, // Dihapus
                // 'selisih_pembiayaan' => $selisihPembiayaan, // Dihapus
                
                'pendapatan_asli_desa' => $pendapatanAsliDesa,
                'pendapatan_transfer' => $pendapatanTransfer,
                'pendapatan_lain' => $pendapatanLain,
                'struktur_belanja' => $strukturBelanjaData, // Menggunakan data belanja yang sudah terstruktur
                // 'pembiayaan' => $pembiayaan // Dihapus
            ];

            // Generate PDF
            $pdf = Pdf::loadView('pdf.templates.laporan_apb_desa', $data)
                ->setPaper('F4', 'portrait');

            // Buat nama file yang deskriptif
            $filename = 'LAPORAN_APB_DESA_' . $tahun . '.pdf';

            // Tawarkan download
            return $pdf->download($filename);

        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Gagal generate PDF APB Desa: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat membuat PDF laporan APB Desa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data pembiayaan untuk tahun tertentu
     * Fungsi ini tidak lagi digunakan secara langsung untuk PDF utama, 
     * namun mungkin masih berguna untuk keperluan lain atau jika pembiayaan ingin ditampilkan kembali.
     */
    protected function getPembiayaanData($tahun)
    {
        // Ambil SiLPA tahun sebelumnya dari tabel total_apb_desa
        $tahun_sebelumnya = $tahun - 1;
        $totalApbTahunSebelumnya = TotalApbDesa::where('tahun_anggaran', $tahun_sebelumnya)->first();
        $silpa_tahun_sebelumnya = $totalApbTahunSebelumnya ? $totalApbTahunSebelumnya->saldo_sisa : 0;

        // Data pembiayaan (contoh statis, sesuaikan dengan data dinamis dari database jika perlu)
        // Untuk saat ini, karena tidak ditampilkan, kita bisa biarkan atau sederhanakan
        return [
            'penerimaan' => [
                ['kode' => '3.1.1', 'uraian' => 'SiLPA Tahun Sebelumnya', 'jumlah' => $silpa_tahun_sebelumnya, 'keterangan' => 'Sisa Lebih Perhitungan Anggaran Tahun Lalu'],
                // ['kode' => '3.1.2', 'uraian' => 'Pencairan Dana Cadangan', 'jumlah' => 0, 'keterangan' => ''],
            ],
            'pengeluaran' => [
                // ['kode' => '3.2.1', 'uraian' => 'Pembentukan Dana Cadangan', 'jumlah' => 0, 'keterangan' => ''],
                // ['kode' => '3.2.2', 'uraian' => 'Penyertaan Modal Desa', 'jumlah' => 0, 'keterangan' => ''],
            ]
        ];
    }

    /**
     * Mendapatkan data struktur belanja untuk PDF.
   
     */
    protected function getStrukturBelanjaData($tahun)
    {
        $belanjaItems = RealisasiBelanja::where('tahun_anggaran', $tahun)
            ->get();

        $struktur = [];
        $bidangKode = 1;

        $belanjaByKategori = $belanjaItems->groupBy('kategori');

        foreach ($belanjaByKategori as $kategoriNama => $itemsKategori) {
            $kegiatanData = [];
            $kegiatanKodeInternal = 1;
            $totalBidang = 0;

            foreach ($itemsKategori as $item) {
                $totalBidang += $item->jumlah;
                $kegiatanData[] = [
                    'kode' => "2.{$bidangKode}.{$kegiatanKodeInternal}",
                    'nama' => $item->deskripsi,
                    'total' => $item->jumlah,
                    'keterangan' => $item->keterangan
                ];
                $kegiatanKodeInternal++;
            }

            $struktur[] = [
                'kode' => "2.{$bidangKode}",
                'nama' => $kategoriNama,
                'total' => $totalBidang,
                'keterangan' => '',
                'kegiatan' => $kegiatanData
            ];
            $bidangKode++;
        }

        return $struktur;
    }
}
