<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseReceiptItem extends Model
{
    protected $guarded = [];

    public function warehouseReceipt()
    {
        return $this->belongsTo(WarehouseReceipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
