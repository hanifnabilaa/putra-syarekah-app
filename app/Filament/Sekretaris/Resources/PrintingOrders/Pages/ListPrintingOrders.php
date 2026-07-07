<?php

namespace App\Filament\Sekretaris\Resources\PrintingOrders\Pages;

use App\Filament\Sekretaris\Resources\PrintingOrders\PrintingOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrintingOrders extends ListRecords
{
    protected static string $resource = PrintingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
