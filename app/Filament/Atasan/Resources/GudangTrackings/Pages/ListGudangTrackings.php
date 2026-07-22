<?php

namespace App\Filament\Atasan\Resources\GudangTrackings\Pages;

use App\Filament\Atasan\Resources\GudangTrackings\GudangTrackingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGudangTrackings extends ListRecords
{
    protected static string $resource = GudangTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
