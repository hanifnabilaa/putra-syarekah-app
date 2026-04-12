<?php

namespace App\Enums;

enum ShipmentMethod: string
{
    case SHIPPING = 'shipping';
    case PICKUP = 'pickup';

    public function label(): string
    {
        return match ($this) {
            self::SHIPPING => 'Dikirim',
            self::PICKUP => 'Ambil Sendiri',
        };
    }
}