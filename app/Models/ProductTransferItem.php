<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTransferItem extends Model
{
    protected $fillable = [
        'product_transfer_id',
        'product_id',
        'quantity',
    ];

    public function productTransfer()
    {
        return $this->belongsTo(ProductTransfer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
