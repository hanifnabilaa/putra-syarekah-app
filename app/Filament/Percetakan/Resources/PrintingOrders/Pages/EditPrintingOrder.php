<?php

namespace App\Filament\Percetakan\Resources\PrintingOrders\Pages;

use App\Filament\Percetakan\Resources\PrintingOrders\PrintingOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrintingOrder extends EditRecord
{
    protected static string $resource = PrintingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
