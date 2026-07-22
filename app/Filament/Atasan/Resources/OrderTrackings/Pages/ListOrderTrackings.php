<?php

namespace App\Filament\Atasan\Resources\OrderTrackings\Pages;

use App\Filament\Atasan\Resources\OrderTrackings\OrderTrackingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrderTrackings extends ListRecords
{
    protected static string $resource = OrderTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
