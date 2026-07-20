<?php

namespace App\Filament\Atasan\Resources\Users\Pages;

use App\Filament\Atasan\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
