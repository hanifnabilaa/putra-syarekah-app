<?php

namespace App\Filament\Keuangan\Resources\Payments\Pages;

use App\Filament\Keuangan\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
