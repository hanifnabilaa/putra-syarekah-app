<?php

namespace App\Filament\Keuangan\Resources\Orders\Pages;

use App\Filament\Keuangan\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
