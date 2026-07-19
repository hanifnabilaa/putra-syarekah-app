<?php

namespace App\Filament\Gudang\Resources\ProductResource\Pages;

use App\Filament\Gudang\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action for gudang
        ];
    }
}
