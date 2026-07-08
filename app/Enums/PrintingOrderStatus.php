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

    /**
     * Valid status transitions from this status.
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::DRAFT => [self::DIKIRIM],
            self::DIKIRIM => [self::PROSES],
            self::PROSES => [self::SELESAI],
            self::SELESAI => [], // Final state, no transitions allowed
        };
    }

    /**
     * Check if transitioning to the given status is allowed.
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions());
    }

    /**
     * Get all available transitions from this status as options for select.
     */
    public function transitionOptions(): array
    {
        $options = [];
        foreach ($this->allowedTransitions() as $status) {
            $options[$status->value] = $status->label();
        }
        return $options;
    }
}
