<?php

namespace App\Filament\Sekretaris\Resources\PrintingOrders\Pages;

use App\Filament\Sekretaris\Resources\PrintingOrders\PrintingOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrintingOrder extends CreateRecord
{
    protected static string $resource = PrintingOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sekretaris_id'] = auth()->id();

        return $data;
    }
}
