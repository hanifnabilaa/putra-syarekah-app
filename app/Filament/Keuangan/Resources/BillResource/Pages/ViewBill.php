<?php

namespace App\Filament\Keuangan\Resources\BillResource\Pages;

use App\Filament\Keuangan\Resources\BillResource;
use App\Models\Payment;
use App\Services\BillService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewBill extends ViewRecord
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
