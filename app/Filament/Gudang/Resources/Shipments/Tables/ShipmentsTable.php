<?php

namespace App\Filament\Gudang\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('order.order_code')
                    ->searchable()
                    ->sortable()
                    ->label('Order Daerah'),
                \Filament\Tables\Columns\TextColumn::make('order.daerah.name')
                    ->searchable()
                    ->sortable()
                    ->label('Daerah'),
                \Filament\Tables\Columns\TextColumn::make('order.bill.status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\BillStatus ? $state->label() : ($state ? (is_string($state) ? ucfirst($state) : $state) : 'Belum Ada Tagihan'))
                    ->color(fn ($state) => $state instanceof \App\Enums\BillStatus ? $state->color() : 'gray')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('order.bill.remaining_bill')
                    ->label('Sisa Tagihan')
                    ->money('IDR')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Status Kirim')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('method')
                    ->label('Metode')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('shipping_date')
                    ->label('Tgl Kirim')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options(collect(\App\Enums\BillStatus::cases())->mapWithKeys(fn ($e) => [$e->value => $e->label()]))
                    ->query(function ($query, array $data) {
                        if (filled($data['value'])) {
                            $query->whereHas('order.bill', function ($q) use ($data) {
                                $q->where('status', $data['value']);
                            });
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
