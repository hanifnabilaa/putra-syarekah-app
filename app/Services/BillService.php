<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class BillService
{
    /**
     * Tambah konfirmasi pembayaran.
     */
    public function addPayment(
        Bill $bill,
        float $amount,
        int $confirmedBy,
        ?string $notes = null,
        ?string $paidAt = null,
        ?string $proofImage = null
    ): Payment {
        return DB::transaction(function () use ($bill, $amount, $confirmedBy, $notes, $paidAt, $proofImage) {
            return $bill->addPayment($amount, $confirmedBy, $notes, $paidAt, $proofImage);
        });
    }

    /**
     * Edit konfirmasi pembayaran dan recalculate.
     */
    public function updatePayment(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $payment->update($data);
            $payment->bill->recalculate();
            return $payment->fresh();
        });
    }

    /**
     * Hapus konfirmasi pembayaran dan recalculate.
     */
    public function deletePayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $bill = $payment->bill;
            $payment->delete();
            $bill->recalculate();
        });
    }
}
