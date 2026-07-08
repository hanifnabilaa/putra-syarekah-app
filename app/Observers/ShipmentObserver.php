<?php

namespace App\Observers;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\StockLog;

class ShipmentObserver
{
    /**
     * Saat status pengiriman berubah ke 'shipped':
     * - Kurangi stok produk
     * - Tambah shipped_quantity di order_item
     * - Catat stock_log tipe 'out' dengan reference
     */
    public function updated(Shipment $shipment): void
    {
        if ($shipment->wasChanged('status') && $shipment->status === ShipmentStatus::SHIPPED) {
            foreach ($shipment->items as $shipmentItem) {
                $orderItem = $shipmentItem->orderItem;
                $product   = $orderItem->product;
                $qty       = $shipmentItem->quantity;

                // Kurangi stok produk
                $product->decrement('stock', $qty);

                // Tambah shipped_quantity di order_item
                $orderItem->increment('shipped_quantity', $qty);

                // Catat stock_log dengan reference (untuk menghindari duplikasi)
                StockLog::create([
                    'product_id'    => $product->id,
                    'user_id'       => auth()->id(),
                    'reference_type' => Shipment::class,
                    'reference_id'  => $shipment->id,
                    'type'          => 'out',
                    'quantity'      => $qty,
                    'notes'         => "Pengiriman #{$shipment->id} — Pesanan {$shipment->order->order_code}",
                ]);
            }
        }
    }
}
