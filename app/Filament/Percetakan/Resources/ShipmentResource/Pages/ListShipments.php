<?php

namespace App\Filament\Percetakan\Resources\ShipmentResource\Pages;

use App\Filament\Percetakan\Resources\ShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipments extends ListRecords
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Pengiriman'),
        ];
    }
}
