<?php

namespace App\Enums;

enum BillStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Unpaid',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::PAID => 'Paid',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'danger',
            self::PARTIALLY_PAID => 'warning',
            self::PAID => 'success',
        };
    }
}