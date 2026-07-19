<?php

namespace App\Models;

use App\Enums\PrintingOrderStatus;
use Illuminate\Database\Eloquent\Model;

class PrintingOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'order_date' => 'date',
        'target_date' => 'date',
        'status' => PrintingOrderStatus::class,
    ];

    // ─── Boot ───────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function (self $order) {
            // Use getRawOriginal to get the string value, not the enum object
            $originalStatus = PrintingOrderStatus::from($order->getRawOriginal('status'));
            $newStatus = $order->status;

            // Skip validation if status hasn't changed
            if ($originalStatus === $newStatus) {
                return;
            }

            // Validate status transition
            if (!$originalStatus->canTransitionTo($newStatus)) {
                throw new \InvalidArgumentException(
                    "Transisi status tidak valid: {$originalStatus->label()} tidak dapat berubah ke {$newStatus->label()}. " .
                    "Status yang diperbolehkan: " . implode(', ', array_map(fn($s) => $s->label(), $originalStatus->allowedTransitions()))
                );
            }
        });

        static::saved(function (self $order) {
            // When printing order is finished, automatically add stock
            if ($order->isDirty('status') && $order->status === PrintingOrderStatus::SELESAI) {
                // Ensure we only process if not already processed
                $hasLogs = \App\Models\StockLog::where('reference_type', self::class)
                    ->where('reference_id', $order->id)
                    ->exists();

                if (!$hasLogs) {
                    foreach ($order->items as $item) {
                        // Increment actual stock in products table
                        if ($item->product) {
                            $item->product->increment('stock', $item->qty);

                            // Create stock log entry
                            \App\Models\StockLog::create([
                                'product_id' => $item->product_id,
                                'user_id' => auth()->id() ?? $order->sekretaris_id,
                                'reference_type' => self::class,
                                'reference_id' => $order->id,
                                'type' => 'in',
                                'quantity' => $item->qty,
                                'notes' => 'Penerimaan otomatis dari penyelesaian PO Percetakan ' . $order->order_code,
                            ]);
                        }
                    }
                }
            }
        });
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Check if order can transition to a new status.
     */
    public function canTransitionTo(PrintingOrderStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    /**
     * Get available next statuses.
     */
    public function getAvailableTransitions(): array
    {
        return $this->status->transitionOptions();
    }

    // ─── Relations ─────────────────────────────────────────────────────────────

    public function sekretaris()
    {
        return $this->belongsTo(User::class, 'sekretaris_id');
    }

    public function percetakan()
    {
        return $this->belongsTo(Percetakan::class);
    }

    public function items()
    {
        return $this->hasMany(PrintingOrderItem::class);
    }

    public function warehouseReceipts()
    {
        return $this->hasMany(WarehouseReceipt::class);
    }
}
