<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTransfer extends Model
{
    protected $fillable = [
        'transfer_code',
        'from_gudang_id',
        'to_gudang_id',
        'transfer_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function fromGudang()
    {
        return $this->belongsTo(User::class, 'from_gudang_id');
    }

    public function toGudang()
    {
        return $this->belongsTo(User::class, 'to_gudang_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(ProductTransferItem::class);
    }
}
