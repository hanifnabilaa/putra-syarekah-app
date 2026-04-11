<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockLog extends Model
{
    use HasFactory;

    protected $table = 'stock_logs';

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
