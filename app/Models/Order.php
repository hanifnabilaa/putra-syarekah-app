<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\ShipmentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'daerah_id',
        'order_code',
        'status',
        'shipping_method',
        'shipping_address',
        'total_bill',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'status'          => OrderStatus::class,
        'shipping_method' => ShipmentMethod::class,
        'total_bill'      => 'decimal:2',
    ];

    // ─── Boot ────────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $order) {
            if (empty($order->order_code)) {
                $order->order_code = self::generateOrderCode();
            }
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public static function generateOrderCode(): string
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $last   = self::where('order_code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('order_code');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function recalculateTotal(): void
    {
        $this->total_bill = $this->items()->sum('subtotal');
        $this->save();
    }

    public function canBeEditedByDaerah(): bool
    {
        return in_array($this->status, [OrderStatus::DRAFT, OrderStatus::SUBMITTED]);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────────

    public function scopeForDaerah($query, int $daerahId)
    {
        return $query->where('daerah_id', $daerahId);
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function daerah()
    {
        return $this->belongsTo(Daerah::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }
}
