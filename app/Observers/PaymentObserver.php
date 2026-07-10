<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    /**
     * Recalculate Bill totals whenever a Payment is created, updated, or deleted.
     */
    public function created(Payment $payment): void
    {
        $this->recalculateBill($payment);
    }

    public function updated(Payment $payment): void
    {
        $this->recalculateBill($payment);
    }

    public function deleted(Payment $payment): void
    {
        $this->recalculateBill($payment);
    }

    /**
     * Recalculate the associated Bill to update total_paid, remaining_bill, and status.
     */
    private function recalculateBill(Payment $payment): void
    {
        if ($payment->bill) {
            $payment->bill->recalculate();
        }
    }
}
