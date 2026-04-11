<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Percetakan
        User::create([
            'name'     => 'Admin Percetakan',
            'username' => 'percetakan',
            'email'    => 'percetakan@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::ADMIN_PERCETAKAN,
        ]);

        // Admin Keuangan
        User::create([
            'name'     => 'Admin Keuangan',
            'username' => 'keuangan',
            'email'    => 'keuangan@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::ADMIN_KEUANGAN,
        ]);
    }
}
