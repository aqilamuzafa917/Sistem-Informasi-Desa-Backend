<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if admin user already exists
        if (User::where('email', 'admin1@desa.com')->exists()) {
            return;
        }

        // Create admin user
        User::create([
            'name' => 'Admin1',
            'email' => 'admin1@desa.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
    }
} 