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
    }

    public function processStockUpdate(): void
    {
        if (in_array($this->status, [ShipmentStatus::SHIPPED, ShipmentStatus::DELIVERED])) {
            $hasLogs = StockLog::where('reference_type', self::class)
                ->where('reference_id', $this->id)
                ->exists();

            if (!$hasLogs) {
                $this->load('items.orderItem.product');

                if ($this->items->isEmpty()) {
                    return;
                }

                $gudangId = auth()->user() ? auth()->user()->getMasterId() : auth()->id();

                foreach ($this->items as $shipmentItem) {
                    $orderItem = $shipmentItem->orderItem;
                    if (!$orderItem) {
                        continue;
                    }

                    $product = $orderItem->product;
                    $qty     = $shipmentItem->quantity;

                    // Kurangi stok pada ProductStock gudang
                    $productStock = ProductStock::firstOrCreate([
                        'product_id' => $product->id,
                        'gudang_id'  => $gudangId,
                    ]);
                    $productStock->decrement('stock', $qty);

                    // Tambah shipped_quantity di order_items
                    $orderItem->increment('shipped_quantity', $qty);

                    // Catat log stok keluar
                    StockLog::create([
                        'product_id'     => $product->id,
                        'user_id'        => $gudangId,
                        'reference_type' => self::class,
                        'reference_id'   => $this->id,
                        'type'           => 'out',
                        'quantity'       => $qty,
                        'notes'          => "Pengiriman #{$this->id} — Pesanan " . ($this->order ? $this->order->order_code : ''),
                    ]);
                }

                // Cek jika seluruh order sudah dikirim penuh
                if ($this->order) {
                    $this->order->load('items');
                    $isFullyShipped = $this->order->items->every(fn ($item) => $item->shipped_quantity >= $item->quantity);

                    if ($isFullyShipped && $this->order->status !== \App\Enums\OrderStatus::FINISHED) {
                        $this->order->update(['status' => \App\Enums\OrderStatus::FINISHED]);
                    }
                }
            }
        }
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
