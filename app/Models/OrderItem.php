<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'shipped_quantity',
    ];

    protected $casts = [
        'quantity'         => 'integer',
        'unit_price'       => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'shipped_quantity' => 'integer',
    ];

    // ─── Accessors ───────────────────────────────────────────────────────────────

    public function getRemainingQuantityAttribute(): int
    {
        return $this->quantity - $this->shipped_quantity;
    }

    public function isFullyShipped(): bool
    {
        return $this->shipped_quantity >= $this->quantity;
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
