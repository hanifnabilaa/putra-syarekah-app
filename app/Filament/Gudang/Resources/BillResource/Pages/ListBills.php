<?php

namespace App\Filament\Gudang\Resources\BillResource\Pages;

use App\Filament\Gudang\Resources\BillResource;
use Filament\Resources\Pages\ListRecords;

class ListBills extends ListRecords
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
