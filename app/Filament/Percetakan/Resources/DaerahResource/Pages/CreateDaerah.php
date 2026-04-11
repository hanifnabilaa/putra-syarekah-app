<?php

namespace App\Filament\Percetakan\Resources\DaerahResource\Pages;

use App\Enums\UserRole;
use App\Filament\Percetakan\Resources\DaerahResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateDaerah extends CreateRecord
{
    protected static string $resource = DaerahResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $userData = $data['user'] ?? [];

            $user = User::create([
                'name'     => $userData['name'],
                'username' => $userData['username'],
                'email'    => $userData['email'] ?? null,
                'password' => Hash::make($userData['password']),
                'role'     => UserRole::DAERAH,
            ]);

            unset($data['user']);

            return $user->daerah()->create($data);
        });
    }
}
