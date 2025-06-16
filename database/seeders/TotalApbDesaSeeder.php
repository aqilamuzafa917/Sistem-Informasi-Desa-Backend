<?php

namespace Database\Seeders;

use App\Models\TotalApbDesa;
use App\Models\RealisasiPendapatan;
use App\Models\RealisasiBelanja;
use App\Models\User;
use App\Http\Controllers\ApbDesaController;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;

class TotalApbDesaSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if data already exists
        if (TotalApbDesa::count() > 0) {
            return;
        }

        // Get admin user or skip if not found
        $admin = User::where('email', 'admin1@desa.com')->first();
        if (!$admin) {
            return;
        }

        // Get unique years from both pendapatan and belanja
        $years = array_unique(array_merge(
            RealisasiPendapatan::pluck('tahun_anggaran')->toArray(),
            RealisasiBelanja::pluck('tahun_anggaran')->toArray()
        ));

        // Create a mock request with the admin user
        $request = new Request();
        $request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        // Use the controller's updateTotalApbDesa function for each year
        $controller = new ApbDesaController();
        foreach ($years as $year) {
            $controller->updateTotalApbDesa($year, $request);
        }
    }
} 