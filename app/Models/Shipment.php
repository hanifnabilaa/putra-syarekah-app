<?php

namespace App\Models;

use App\Enums\ShipmentMethod;
use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'queue_order',
        'status',
        'method',
        'shipping_address',
        'shipping_date',
        'notes',
        'proof_of_delivery',
    ];

    protected $casts = [
        'status'        => ShipmentStatus::class,
        'method'        => ShipmentMethod::class,
        'shipping_date' => 'date',
        'queue_order'   => 'integer',
    ];

    // ─── Boot ────────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $shipment) {
            if (empty($shipment->queue_order) || $shipment->queue_order === 0) {
                $shipment->queue_order = (self::max('queue_order') ?? 0) + 1;
            }
        });

        static::saved(function (self $shipment) {
            // When shipment is sent (shipped/delivered), decrement stock
            if (in_array($shipment->status, [ShipmentStatus::SHIPPED, ShipmentStatus::DELIVERED])) {
                $hasLogs = \App\Models\StockLog::where('reference_type', self::class)
                    ->where('reference_id', $shipment->id)
                    ->exists();

                if (!$hasLogs) {
                    // ShipmentItem has order_item_id which has product_id
                    foreach ($shipment->items as $item) {
                        \App\Models\StockLog::create([
                            'product_id' => $item->orderItem->product_id,
                            'user_id' => auth()->id(),
                            'reference_type' => self::class,
                            'reference_id' => $shipment->id,
                            'type' => 'out',
                            'quantity' => $item->quantity,
                            'notes' => 'Pengiriman ke pesanan ' . $shipment->order->order_code,
                        ]);
                    }
                }
            }
        });
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
