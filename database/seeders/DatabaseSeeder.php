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
            TotalApbDesaSeeder::class,
            RealisasiBelanjaSeeder::class,
            RealisasiPendapatanSeeder::class,
        ]);
    }
}
