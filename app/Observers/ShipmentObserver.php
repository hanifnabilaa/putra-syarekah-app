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

                // Kurangi stok produk untuk gudang ini
                $productStock = \App\Models\ProductStock::firstOrCreate([
                    'product_id' => $product->id,
                    'gudang_id'  => auth()->user() ? auth()->user()->getMasterId() : auth()->id(),
                ]);
                $productStock->decrement('stock', $qty);

                // Tambah shipped_quantity di order_item
                $orderItem->increment('shipped_quantity', $qty);

                // Catat stock_log dengan reference (untuk menghindari duplikasi)
                StockLog::create([
                    'product_id'    => $product->id,
                    'user_id'       => auth()->user() ? auth()->user()->getMasterId() : auth()->id(),
                    'reference_type' => Shipment::class,
                    'reference_id'  => $shipment->id,
                    'type'          => 'out',
                    'quantity'      => $qty,
                    'notes'         => "Pengiriman #{$shipment->id} — Pesanan {$shipment->order->order_code}",
                ]);
            }

            // Check if the whole order is fully shipped
            $order = $shipment->order;
            $isFullyShipped = true;
            
            // Refresh order items to get the latest shipped_quantity
            $order->load('items');
            
            foreach ($order->items as $item) {
                if ($item->shipped_quantity < $item->quantity) {
                    $isFullyShipped = false;
                    break;
                }
            }

            if ($isFullyShipped && $order->status !== \App\Enums\OrderStatus::FINISHED) {
                $order->update(['status' => \App\Enums\OrderStatus::FINISHED]);
            }
        }
    }
}
