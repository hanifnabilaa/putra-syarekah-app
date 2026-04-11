<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;

class OrderObserver
{
    /**
     * Saat pesanan di-approve → buat tagihan otomatis.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->status === OrderStatus::APPROVED) {
            // Create bill if not exists
            if (! $order->bill()->exists()) {
                $order->bill()->create([
                    'total_bill'     => $order->total_bill,
                    'total_paid'     => 0,
                    'remaining_bill' => $order->total_bill,
                ]);
            }
        }
    }
}
