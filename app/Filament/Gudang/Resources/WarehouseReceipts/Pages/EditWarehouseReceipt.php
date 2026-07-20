<?php

namespace App\Filament\Gudang\Resources\WarehouseReceipts\Pages;

use App\Filament\Gudang\Resources\WarehouseReceipts\WarehouseReceiptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWarehouseReceipt extends EditRecord
{
    protected static string $resource = WarehouseReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->processStockUpdate();
    }
}
