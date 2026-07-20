<?php

namespace App\Enums;

enum UserRole: string
{
    case SEKRETARIS = 'sekretaris';
    case PERCETAKAN = 'percetakan';
    case KARYAWAN_PERCETAKAN = 'karyawan_percetakan';
    case GUDANG = 'gudang';
    case KARYAWAN_GUDANG = 'karyawan_gudang';
    case DAERAH = 'daerah';
    case KEUANGAN = 'keuangan';
    case ATASAN = 'atasan';

    public function label(): string
    {
        return match ($this) {
            self::SEKRETARIS => 'Sekretaris',
            self::PERCETAKAN => 'Percetakan',
            self::KARYAWAN_PERCETAKAN => 'Karyawan Percetakan',
            self::GUDANG => 'Gudang',
            self::KARYAWAN_GUDANG => 'Karyawan Gudang',
            self::DAERAH => 'Daerah',
            self::KEUANGAN => 'Keuangan',
            self::ATASAN => 'Atasan',
        };
    }
}
