<?php

namespace App\Filament\Percetakan\Resources\PrintingOrders\Pages;

use App\Filament\Percetakan\Resources\PrintingOrders\PrintingOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrintingOrder extends CreateRecord
{
    protected static string $resource = PrintingOrderResource::class;
}
