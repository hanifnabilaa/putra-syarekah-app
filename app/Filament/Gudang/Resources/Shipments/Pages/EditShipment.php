<?php

namespace App\Filament\Gudang\Resources\Shipments\Pages;

use App\Filament\Gudang\Resources\Shipments\ShipmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShipment extends EditRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function afterSave(): void
    {
        $this->record->processStockUpdate();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
