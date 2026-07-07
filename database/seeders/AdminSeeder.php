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
        // Atasan (Super Admin)
        User::create([
            'name'     => 'Atasan (Super Admin)',
            'username' => 'atasan',
            'email'    => 'atasan@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::ATASAN,
        ]);

        // Sekretaris
        User::create([
            'name'     => 'Sekretaris',
            'username' => 'sekretaris',
            'email'    => 'sekretaris@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::SEKRETARIS,
        ]);

        // Gudang
        User::create([
            'name'     => 'Admin Gudang',
            'username' => 'gudang',
            'email'    => 'gudang@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::GUDANG,
        ]);

        // Keuangan
        User::create([
            'name'     => 'Admin Keuangan',
            'username' => 'keuangan',
            'email'    => 'keuangan@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::KEUANGAN,
        ]);

        // Percetakan 1
        $percetakanA = User::create([
            'name'     => 'Percetakan A',
            'username' => 'percetakana',
            'email'    => 'percetakana@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::PERCETAKAN,
        ]);
        \App\Models\Percetakan::create([
            'user_id' => $percetakanA->id,
            'name' => 'Percetakan A',
            'address' => 'Jl. Percetakan A',
        ]);

        // Percetakan 2
        $percetakanB = User::create([
            'name'     => 'Percetakan B',
            'username' => 'percetakanb',
            'email'    => 'percetakanb@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => UserRole::PERCETAKAN,
        ]);
        \App\Models\Percetakan::create([
            'user_id' => $percetakanB->id,
            'name' => 'Percetakan B',
            'address' => 'Jl. Percetakan B',
        ]);
    }
}
