<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
        'image'     => 'array',
    ];

    // ─── Accessors ──────────────────────────────────────────────────────────────

    public function getImageUrlsAttribute(): array
    {
        if (empty($this->image)) {
            return [];
        }

        return array_map(function ($path) {
            return Storage::disk('public')->url($path);
        }, is_string($this->image) ? [$this->image] : $this->image);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->productStocks()->sum('stock');
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Relations ──────────────────────────────────────────────────────────────

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }
}
