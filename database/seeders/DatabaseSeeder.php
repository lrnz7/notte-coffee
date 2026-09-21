<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Buat Akun Multi-Role
        User::create([
            'name' => 'Owner / Admin Notte',
            'email' => 'admin@notte.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir Jaga',
            'email' => 'kasir@notte.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
        ]);

        User::create([
            'name' => 'Lorenzo Calvin',
            'email' => 'lorenzo@notte.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        // 2. Panggil Seeder ERP Notte (Bahan Baku & Menu)
        $this->call(NotteSeeder::class);
    }
}