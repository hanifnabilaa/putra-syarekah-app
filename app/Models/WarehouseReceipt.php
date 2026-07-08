<?php

namespace App\Models;

use App\Enums\WarehouseReceiptStatus;
use Illuminate\Database\Eloquent\Model;

class WarehouseReceipt extends Model
{
    protected $guarded = [];

    protected $casts = [
        'receipt_date' => 'date',
        'status' => WarehouseReceiptStatus::class,
    ];

    public function printingOrder()
    {
        return $this->belongsTo(PrintingOrder::class);
    }

    public function gudang()
    {
        return $this->belongsTo(User::class, 'gudang_id');
    }

    public function items()
    {
        return $this->hasMany(WarehouseReceiptItem::class);
    }

    protected static function booted()
    {
        static::saved(function ($receipt) {
            if ($receipt->status === WarehouseReceiptStatus::SELESAI_PRODUKSI) {
                // Ensure we only process if not already processed
                // To prevent duplicate logs, we check if stock logs already exist for this receipt
                $hasLogs = \App\Models\StockLog::where('reference_type', self::class)
                    ->where('reference_id', $receipt->id)
                    ->exists();

                if (!$hasLogs) {
                    foreach ($receipt->items as $item) {
                        // Increment actual stock in products table
                        $item->product->increment('stock', $item->qty_received);

                        // Create stock log entry
                        \App\Models\StockLog::create([
                            'product_id' => $item->product_id,
                            'user_id' => $receipt->gudang_id,
                            'reference_type' => self::class,
                            'reference_id' => $receipt->id,
                            'type' => 'in',
                            'quantity' => $item->qty_received,
                            'notes' => 'Penerimaan otomatis dari ' . $receipt->receipt_code,
                        ]);
                    }
                }
            }
        });
    }
}
