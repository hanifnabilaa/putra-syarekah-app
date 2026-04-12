<?php

namespace Database\Seeders;

use App\Models\Daerah;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Percetakan
        User::create([
            'username'     => 'percetakan',
            'name'     => 'Admin Percetakan',
            'email'    => 'percetakan@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => 'admin_percetakan',
        ]);

        // Admin Keuangan
        User::create([
            'username'     => 'keuangan',
            'name'     => 'Admin Keuangan',
            'email'    => 'keuangan@putrasyarekah.com',
            'password' => Hash::make('password'),
            'role'     => 'admin_keuangan',
        ]);

        // User Daerah (contoh)
        $userDaerah = User::create([
            'username'     => 'bandung',
            'name'     => 'Daerah Bandung',
            'email'    => 'bandung@daerah.com',
            'password' => Hash::make('password'),
            'role'     => 'daerah',
        ]);

        Daerah::create([
            'user_id'                => $userDaerah->id,
            'name'                   => 'Daerah Bandung',
            'pic_name'               => 'Ahmad Fauzi',
            'pic_phone'              => '08123456789',
            'address'                => 'Jl. Contoh No. 1, Bandung',
            'phone'                  => '02212345678',
            'is_active'              => true,
        ]);
    }
}
