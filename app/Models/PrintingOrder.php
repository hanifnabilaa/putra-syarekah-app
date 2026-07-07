<?php

namespace App\Models;

use App\Enums\PrintingOrderStatus;
use Illuminate\Database\Eloquent\Model;

class PrintingOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'order_date' => 'date',
        'target_date' => 'date',
        'status' => PrintingOrderStatus::class,
    ];

    public function sekretaris()
    {
        return $this->belongsTo(User::class, 'sekretaris_id');
    }

    public function percetakan()
    {
        return $this->belongsTo(Percetakan::class);
    }

    public function items()
    {
        return $this->hasMany(PrintingOrderItem::class);
    }

    public function warehouseReceipts()
    {
        return $this->hasMany(WarehouseReceipt::class);
    }
}
