<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'order_item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
