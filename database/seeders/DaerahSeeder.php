<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Daerah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DaerahSeeder extends Seeder
{
    public function run(): void
    {
        $daerahs = [
            [
                'name'      => 'Daerah Jakarta Selatan',
                'pic_name'  => 'Budi Santoso',
                'pic_phone' => '081234567890',
                'address'   => 'Jl. Sudirman No. 1, Jakarta Selatan',
                'phone'     => '02112345678',
                'username'  => 'jakarta.selatan',
                'email'     => 'jaksel@daerah.com',
            ],
            [
                'name'      => 'Daerah Bandung',
                'pic_name'  => 'Siti Rahayu',
                'pic_phone' => '082345678901',
                'address'   => 'Jl. Braga No. 10, Bandung',
                'phone'     => '02298765432',
                'username'  => 'bandung',
                'email'     => 'bandung@daerah.com',
            ],
            [
                'name'      => 'Daerah Surabaya',
                'pic_name'  => 'Ahmad Fauzi',
                'pic_phone' => '083456789012',
                'address'   => 'Jl. Tunjungan No. 5, Surabaya',
                'phone'     => '03112345678',
                'username'  => 'surabaya',
                'email'     => 'surabaya@daerah.com',
            ],
        ];

        foreach ($daerahs as $data) {
            $user = User::create([
                'name'     => $data['name'],
                'username' => $data['username'],
                'email'    => $data['email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role'     => UserRole::DAERAH,
            ]);

            $user->daerah()->create([
                'name'      => $data['name'],
                'pic_name'  => $data['pic_name'],
                'pic_phone' => $data['pic_phone'],
                'address'   => $data['address'],
                'phone'     => $data['phone'],
                'is_active' => true,
            ]);
        }
    }
}
