<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PendudukSeeder::class,
            RealisasiPendapatanSeeder::class,
            RealisasiBelanjaSeeder::class,
            TotalApbDesaSeeder::class,
            PengaduanSeeder::class,
        ]);
    }
}
