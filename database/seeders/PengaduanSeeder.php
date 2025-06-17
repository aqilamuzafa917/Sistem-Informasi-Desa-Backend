<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengaduan;

class PengaduanSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 10 data dummy pengaduan
        Pengaduan::factory()->count(5)->create();
    }
}
