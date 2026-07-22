<?php

namespace App\Filament\Atasan\Resources\OrderTrackings\Pages;

use App\Filament\Atasan\Resources\OrderTrackings\OrderTrackingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrderTracking extends ViewRecord
{
    protected static string $resource = OrderTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
