<?php

namespace App\Filament\Sekretaris\Resources\Orders\Pages;

use App\Filament\Sekretaris\Resources\Orders\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
