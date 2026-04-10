<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN_PERCETAKAN = 'admin_percetakan';
    case ADMIN_KEUANGAN = 'admin_keuangan';
    case DAERAH = 'daerah';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN_PERCETAKAN => 'Admin Percetakan',
            self::ADMIN_KEUANGAN => 'Admin Keuangan',
            self::DAERAH => 'Daerah',
        };
    }
}
