<?php

namespace App\Filament\Atasan\Resources\OrderTrackings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class OrderTrackingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('order_code')
                    ->label('Kode Pesanan')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('daerah.name')
                    ->label('Daerah')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Pesanan')
                    ->date('d M Y')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('bill.remaining_bill')
                    ->label('Sisa Tagihan')
                    ->money('IDR')
                    ->color(fn ($state) => (float) $state > 0 ? 'danger' : 'success')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Status Order')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\OrderStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof \App\Enums\OrderStatus ? $state->color() : 'gray')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('fulfillment_progress')
                    ->label('Progress Pengiriman')
                    ->getStateUsing(function (\App\Models\Order $record) {
                        $totalOrdered = $record->items->sum('quantity');
                        $totalShipped = $record->items->sum('shipped_quantity');
                        if ($totalOrdered == 0) return '0%';
                        $percent = round(($totalShipped / $totalOrdered) * 100);
                        return "{$percent}% ({$totalShipped}/{$totalOrdered})";
                    })
                    ->badge()
                    ->color(function ($state) {
                        if (str_starts_with($state, '100%')) return 'success';
                        if (str_starts_with($state, '0%')) return 'danger';
                        return 'warning';
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Status Order')
                    ->options(collect(\App\Enums\OrderStatus::cases())->mapWithKeys(fn ($e) => [$e->value => $e->label()])),
                \Filament\Tables\Filters\SelectFilter::make('daerah')
                    ->label('Daerah')
                    ->relationship('daerah', 'name'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                // Read-only
            ]);
    }
}
