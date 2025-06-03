<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DesaConfigController extends Controller
{
    public function getConfig()
    {
        $config = config('desa');
        return response()->json($config);
    }

    public function updateConfig(Request $request)
    {
        $config = config('desa');
        $newConfig = $request->all();
        
        // Validasi input
        $request->validate([
            'kode' => 'required|string',
            'nama_kabupaten' => 'required|string',
            'nama_kecamatan' => 'required|string',
            'nama_desa' => 'required|string',
            'alamat_desa' => 'required|string',
            'kode_pos' => 'required|string',
            'nama_provinsi' => 'required|string',
            'jabatan_kepala' => 'required|string',
            'nama_kepala_desa' => 'required|string',
            'jabatan_ttd' => 'required|string',
            'nama_pejabat_ttd' => 'required|string',
            'nip_pejabat_ttd' => 'nullable|string',
            'sosial_media' => 'nullable|string',
            'website_desa' => 'nullable|string',
            'email_desa' => 'nullable|email',
            'telepon_desa' => 'nullable|string',
        ]);

        // Update konfigurasi
        $config = array_merge($config, $newConfig);
        
        // Simpan ke file konfigurasi
        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        File::put(config_path('desa.php'), $configContent);

        return response()->json([
            'message' => 'Konfigurasi berhasil diperbarui',
            'config' => $config
        ]);
    }
} 