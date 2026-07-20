<?php

namespace App\Filament\Gudang\Resources\ProductTransfers\Pages;

use App\Filament\Gudang\Resources\ProductTransfers\ProductTransferResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductTransfer extends ViewRecord
{
    protected static string $resource = ProductTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
