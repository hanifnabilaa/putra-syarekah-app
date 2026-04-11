<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Daerah;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Buat pesanan baru beserta item-itemnya.
     *
     * @param Daerah $daerah
     * @param array $data = [
     *   'shipping_method' => 'shipping'|'pickup',
     *   'shipping_address'=> string|null,
     *   'notes'           => string|null,
     *   'items'           => [['product_id' => int, 'quantity' => int], ...]
     * ]
     */
    public function createOrder(Daerah $daerah, array $data): Order
    {
        return DB::transaction(function () use ($daerah, $data) {
            $order = $daerah->orders()->create([
                'shipping_method'  => $data['shipping_method'],
                'shipping_address' => $data['shipping_address'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'status'           => OrderStatus::DRAFT,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $product  = \App\Models\Product::findOrFail($item['product_id']);
                $qty      = $item['quantity'];
                $price    = $product->price;
                $subtotal = $price * $qty;
                $total   += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'subtotal'   => $subtotal,
                ]);
            }

            $order->update(['total_bill' => $total, 'status' => OrderStatus::SUBMITTED]);

            return $order->fresh(['items.product']);
        });
    }

    /**
     * Update pesanan (untuk edit oleh daerah — hanya saat draft/submitted).
     */
    public function updateOrder(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'shipping_method'  => $data['shipping_method'] ?? $order->shipping_method,
                'shipping_address' => $data['shipping_address'] ?? $order->shipping_address,
                'notes'            => $data['notes'] ?? $order->notes,
            ]);

            if (isset($data['items'])) {
                $order->items()->delete();
                $total = 0;
                foreach ($data['items'] as $item) {
                    $product  = \App\Models\Product::findOrFail($item['product_id']);
                    $qty      = $item['quantity'];
                    $price    = $product->price;
                    $subtotal = $price * $qty;
                    $total   += $subtotal;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $qty,
                        'unit_price' => $price,
                        'subtotal'   => $subtotal,
                    ]);
                }
                $order->update(['total_bill' => $total]);
            }

            return $order->fresh(['items.product']);
        });
    }

    /**
     * Approve pesanan — ubah status ke approved (observer akan buat tagihan).
     */
    public function approveOrder(Order $order): Order
    {
        $order->update(['status' => OrderStatus::APPROVED]);
        return $order->fresh();
    }

    /**
     * Tolak pesanan dengan alasan.
     */
    public function rejectOrder(Order $order, string $reason): Order
    {
        $order->update([
            'status'           => OrderStatus::REJECTED,
            'rejection_reason' => $reason,
        ]);
        return $order->fresh();
    }

    /**
     * Batalkan pesanan.
     */
    public function cancelOrder(Order $order): Order
    {
        $order->update(['status' => OrderStatus::REJECTED]);
        return $order->fresh();
    }
}
