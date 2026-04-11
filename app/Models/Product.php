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
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'stock'     => 'integer',
        'is_active' => 'boolean',
    ];

    // ─── Accessors ──────────────────────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
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
}
