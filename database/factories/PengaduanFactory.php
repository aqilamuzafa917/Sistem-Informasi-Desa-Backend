<?php

namespace Database\Factories;

use App\Models\Pengaduan;
use App\Enums\KategoriPengaduan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengaduanFactory extends Factory
{
    protected $model = Pengaduan::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'nomor_telepon' => $this->faker->phoneNumber(),
            'kategori' => $this->faker->randomElement(KategoriPengaduan::cases()),
            'detail_pengaduan' => $this->faker->sentence(10),
            'status' => 'Diajukan',
            'media' => [],
        ];
    }
}
