<?php

namespace App\Models;

use App\Enums\ShipmentMethod;
use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'queue_order',
        'status',
        'method',
        'shipping_address',
        'shipping_date',
        'notes',
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
