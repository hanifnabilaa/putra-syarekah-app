<?php

namespace App\Filament\Gudang\Resources\WarehouseReceipts\Pages;

use App\Filament\Gudang\Resources\WarehouseReceipts\WarehouseReceiptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWarehouseReceipts extends ListRecords
{
    protected static string $resource = WarehouseReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
