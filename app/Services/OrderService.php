<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Daerah;
use App\Models\Order;
use App\Models\StockLog;
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
     * Tidak mengizinkan edit jika pesanan sudah memiliki pengiriman.
     *
     * @throws \InvalidArgumentException Jika pesanan tidak dapat diedit
     */
    public function updateOrder(Order $order, array $data): Order
    {
        // Cek apakah pesanan masih bisa diedit oleh daerah
        if (!$order->canBeEditedByDaerah()) {
            throw new \InvalidArgumentException(
                "Pesanan tidak dapat diedit dalam status '{$order->status->label()}'. " .
                "Pesanan hanya dapat diedit saat berstatus Draft atau Submitted."
            );
        }

        // Cek apakah ada item yang sudah dikirim
        $hasShippedItems = $order->items()->where('shipped_quantity', '>', 0)->exists();
        if ($hasShippedItems) {
            throw new \InvalidArgumentException(
                "Pesanan tidak dapat diedit karena beberapa item sudah dikirim."
            );
        }

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
     * Tolak pesanan dengan alasan dan kembalikan stok untuk item yang sudah dikirim.
     */
    public function rejectOrder(Order $order, string $reason): Order
    {
        return DB::transaction(function () use ($order, $reason) {
            // Kembalikan stok untuk item yang sudah dikirim
            $this->restoreShippedStock($order, "Pembatalan pesanan {$order->order_code}: {$reason}");

            $order->update([
                'status'           => OrderStatus::REJECTED,
                'rejection_reason' => $reason,
            ]);

            return $order->fresh();
        });
    }

    /**
     * Batalkan pesanan dan kembalikan stok untuk item yang sudah dikirim.
     */
    public function cancelOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            // Kembalikan stok untuk item yang sudah dikirim
            $this->restoreShippedStock($order, "Pembatalan pesanan {$order->order_code}");

            $order->update(['status' => OrderStatus::REJECTED]);

            return $order->fresh();
        });
    }

    /**
     * Kembalikan stok produk untuk item yang sudah dikirim dalam pesanan.
     */
    private function restoreShippedStock(Order $order, string $reason): void
    {
        foreach ($order->items as $item) {
            if ($item->shipped_quantity > 0) {
                // Kembalikan stok produk
                $item->product->increment('stock', $item->shipped_quantity);

                // Catat stock log untuk audit
                StockLog::create([
                    'product_id'    => $item->product_id,
                    'user_id'      => auth()->id(),
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'type'         => 'in',
                    'quantity'     => $item->shipped_quantity,
                    'notes'        => $reason,
                ]);
            }
        }
    }
}
