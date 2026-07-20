<?php

namespace App\Filament\Gudang\Resources\ProductTransfers\Pages;

use App\Filament\Gudang\Resources\ProductTransfers\ProductTransferResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductTransfers extends ListRecords
{
    protected static string $resource = ProductTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
