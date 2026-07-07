<?php

namespace App\Filament\Sekretaris\Resources\PrintingOrders\Pages;

use App\Filament\Sekretaris\Resources\PrintingOrders\PrintingOrderResource;
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
