<?php

namespace App\Enums;

enum WarehouseReceiptStatus: string
{
    case BELUM_PRODUKSI = 'belum_produksi';
    case PRODUKSI_SEBAGIAN = 'produksi_sebagian';
    case SELESAI_PRODUKSI = 'selesai_produksi';

    public function label(): string
    {
        return match ($this) {
            self::BELUM_PRODUKSI => 'Belum Produksi',
            self::PRODUKSI_SEBAGIAN => 'Produksi Sebagian',
            self::SELESAI_PRODUKSI => 'Selesai Produksi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BELUM_PRODUKSI => 'gray',
            self::PRODUKSI_SEBAGIAN => 'warning',
            self::SELESAI_PRODUKSI => 'success',
        };
    }
}
