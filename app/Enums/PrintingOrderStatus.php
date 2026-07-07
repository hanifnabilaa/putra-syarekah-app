<?php

namespace App\Enums;

enum PrintingOrderStatus: string
{
    case DRAFT = 'draft';
    case DIKIRIM = 'dikirim';
    case PROSES = 'proses';
    case SELESAI = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::DIKIRIM => 'Dikirim ke Percetakan',
            self::PROSES => 'Proses Produksi',
            self::SELESAI => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::DIKIRIM => 'warning',
            self::PROSES => 'info',
            self::SELESAI => 'success',
        };
    }
}
