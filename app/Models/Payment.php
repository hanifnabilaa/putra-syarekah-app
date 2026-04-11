<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'amount',
        'confirmed_by',
        'payment_date',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
