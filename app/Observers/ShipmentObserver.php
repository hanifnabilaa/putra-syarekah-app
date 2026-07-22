<?php

namespace App\Observers;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\StockLog;

class ShipmentObserver
{
    public function created(Shipment $shipment): void
    {
        $shipment->processStockUpdate();
    }

    public function updated(Shipment $shipment): void
    {
        $shipment->processStockUpdate();
    }
}
