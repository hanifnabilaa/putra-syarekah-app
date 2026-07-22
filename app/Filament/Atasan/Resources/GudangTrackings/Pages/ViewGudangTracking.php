<?php

namespace App\Filament\Atasan\Resources\GudangTrackings\Pages;

use App\Filament\Atasan\Resources\GudangTrackings\GudangTrackingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGudangTracking extends ViewRecord
{
    protected static string $resource = GudangTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
