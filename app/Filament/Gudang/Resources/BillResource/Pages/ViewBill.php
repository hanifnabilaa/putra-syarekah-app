<?php

namespace App\Filament\Gudang\Resources\BillResource\Pages;

use App\Filament\Gudang\Resources\BillResource;
use Filament\Resources\Pages\ViewRecord;

class ViewBill extends ViewRecord
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
