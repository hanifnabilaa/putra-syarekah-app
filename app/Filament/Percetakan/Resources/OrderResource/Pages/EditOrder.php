<?php

namespace App\Filament\Percetakan\Resources\OrderResource\Pages;

use App\Filament\Percetakan\Resources\OrderResource;
use App\Services\OrderService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterSave(): void
    {
        // Recalculate total after edit
        $items    = $this->record->items;
        $total    = $items->sum(fn ($i) => $i->unit_price * $i->quantity);
        $this->record->update(['total_bill' => $total]);

        // Update subtotal for each item
        foreach ($items as $item) {
            $item->update(['subtotal' => $item->unit_price * $item->quantity]);
        }

        // Sync bill if exists
        if ($this->record->bill) {
            $diff = $total - $this->record->bill->total_bill;
            $this->record->bill->update([
                'total_bill'     => $total,
                'remaining_bill' => max(0, $this->record->bill->remaining_bill + $diff),
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
