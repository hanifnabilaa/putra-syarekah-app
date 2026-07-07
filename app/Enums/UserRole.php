<?php

namespace App\Enums;

enum UserRole: string
{
    case SEKRETARIS = 'sekretaris';
    case PERCETAKAN = 'percetakan';
    case GUDANG = 'gudang';
    case DAERAH = 'daerah';
    case KEUANGAN = 'keuangan';
    case ATASAN = 'atasan';

    public function label(): string
    {
        return match ($this) {
            self::SEKRETARIS => 'Sekretaris',
            self::PERCETAKAN => 'Percetakan',
            self::GUDANG => 'Gudang',
            self::DAERAH => 'Daerah',
            self::KEUANGAN => 'Keuangan',
            self::ATASAN => 'Atasan',
        };
    }
}
