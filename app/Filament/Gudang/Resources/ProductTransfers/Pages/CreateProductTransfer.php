<?php

namespace App\Filament\Gudang\Resources\ProductTransfers\Pages;

use App\Filament\Gudang\Resources\ProductTransfers\ProductTransferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductTransfer extends CreateRecord
{
    protected static string $resource = ProductTransferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['transfer_code'] = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        $data['from_gudang_id'] = auth()->id();
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $transfer = $this->record;
        
        foreach ($transfer->items as $item) {
            // Deduct from sender
            $senderStock = \App\Models\ProductStock::firstOrCreate(
                ['product_id' => $item->product_id, 'gudang_id' => $transfer->from_gudang_id],
                ['stock' => 0]
            );
            $senderStock->stock -= $item->quantity;
            $senderStock->save();

            \App\Models\StockLog::create([
                'product_id' => $item->product_id,
                'user_id' => $transfer->from_gudang_id,
                'reference_type' => \App\Models\ProductTransfer::class,
                'reference_id' => $transfer->id,
                'type' => 'out',
                'quantity' => $item->quantity,
                'notes' => 'Transfer ke ' . ($transfer->toGudang->name ?? 'Gudang Tujuan'),
            ]);

            // Add to receiver
            $receiverStock = \App\Models\ProductStock::firstOrCreate(
                ['product_id' => $item->product_id, 'gudang_id' => $transfer->to_gudang_id],
                ['stock' => 0]
            );
            $receiverStock->stock += $item->quantity;
            $receiverStock->save();

            \App\Models\StockLog::create([
                'product_id' => $item->product_id,
                'user_id' => $transfer->to_gudang_id,
                'reference_type' => \App\Models\ProductTransfer::class,
                'reference_id' => $transfer->id,
                'type' => 'in',
                'quantity' => $item->quantity,
                'notes' => 'Transfer dari ' . ($transfer->fromGudang->name ?? 'Gudang Pengirim'),
            ]);
        }
    }
}
