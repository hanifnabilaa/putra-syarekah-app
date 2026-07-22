<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'pic_name',
        'pic_phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class, 'gudang_id', 'user_id');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class, 'user_id', 'user_id');
    }

    public function shipments()
    {
        // Karena pengiriman dikirim oleh user gudang, asumsikan user_id pada Shipment adalah ID gudang
        // Jika tidak, kita bisa abaikan relation ini jika susah.
    }

    public function warehouseReceipts()
    {
        return $this->hasMany(WarehouseReceipt::class, 'gudang_id', 'user_id');
    }
}
