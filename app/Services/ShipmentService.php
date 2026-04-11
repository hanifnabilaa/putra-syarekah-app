<?php

namespace App\Services;

use App\Enums\ShipmentMethod;
use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    /**
     * Buat pengiriman baru (parsial boleh).
     *
     * @param Order $order
     * @param array $items = [['order_item_id' => int, 'quantity' => int], ...]
     * @param array $data  = ['shipping_date' => string|null, 'notes' => string|null, 'method' => string|null]
     */
    public function createShipment(Order $order, array $items, array $data = []): Shipment
    {
        return DB::transaction(function () use ($order, $items, $data) {
            $shipment = $order->shipments()->create([
                'status'           => ShipmentStatus::PENDING,
                'method'           => $data['method'] ?? $order->shipping_method->value,
                'shipping_address' => $data['shipping_address'] ?? $order->shipping_address,
                'shipping_date'    => $data['shipping_date'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $shipment->items()->create([
                    'order_item_id' => $item['order_item_id'],
                    'quantity'      => $item['quantity'],
                ]);
            }

            return $shipment->fresh(['items.orderItem.product']);
        });
    }

    /**
     * Update status pengiriman.
     * Saat shipped → observer akan auto update stok & shipped_quantity.
     */
    public function updateStatus(Shipment $shipment, ShipmentStatus $status): Shipment
    {
        $shipment->update(['status' => $status]);
        return $shipment->fresh();
    }
}
