<?php

namespace App\Filament\Gudang\Resources\Shipments\Pages;

use App\Filament\Gudang\Resources\Shipments\ShipmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function afterCreate(): void
    {
        $this->record->processStockUpdate();
    }
}
