<?php

namespace App\Models;

use App\Enums\BillStatus;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'total_bill',
        'total_paid',
        'remaining_bill',
        'status',
    ];

    protected $casts = [
        'total_bill'     => 'decimal:2',
        'total_paid'     => 'decimal:2',
        'remaining_bill' => 'decimal:2',
        'status'         => BillStatus::class,
    ];

    // ─── Business Logic ──────────────────────────────────────────────────────────

    /**
     * Tambah pembayaran dan update status bill + order.
     */
    public function addPayment(float $amount, int $confirmedBy, ?string $notes = null, ?string $paidAt = null, ?string $proofImage = null): Payment
    {
        $payment = $this->payments()->create([
            'amount'       => $amount,
            'confirmed_by' => $confirmedBy,
            'payment_date' => $paidAt ?? now()->toDateString(),
            'confirmed_at' => now(),
            'notes'        => $notes,
            'proof_image'  => $proofImage,
        ]);

        $this->recalculate();

        return $payment;
    }

    /**
     * Recalculate totals and update status.
     */
    public function recalculate(): void
    {
        $this->total_paid     = $this->payments()->sum('amount');
        $this->remaining_bill = max(0, $this->total_bill - $this->total_paid);

        if ($this->total_paid <= 0) {
            $this->status = BillStatus::UNPAID;
        } elseif ($this->remaining_bill > 0) {
            $this->status = BillStatus::PARTIALLY_PAID;
        } else {
            $this->status = BillStatus::PAID;
            // Mark order as finished
            $this->order()->update(['status' => OrderStatus::FINISHED->value]);
        }

        $this->save();
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
