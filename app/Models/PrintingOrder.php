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
