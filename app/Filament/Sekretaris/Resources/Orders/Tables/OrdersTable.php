<?php

namespace App\Filament\Sekretaris\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Filament\Sekretaris\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode Pesanan')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('daerah.name')
                    ->label('Daerah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Jml Produk')
                    ->counts('items'),

                TextColumn::make('total_bill')
                    ->label('Total')
                    ->money('IDR'),

                TextColumn::make('shipping_method')
                    ->label('Pengiriman')
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\ShipmentMethod ? $state->label() : $state),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof OrderStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof OrderStatus ? $state->color() : 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($e) => [$e->value => $e->label()])),

                SelectFilter::make('daerah_id')
                    ->label('Daerah')
                    ->relationship('daerah', 'name'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === OrderStatus::SUBMITTED)
                    ->requiresConfirmation()
                    ->action(function (Order $record): void {
                        app(OrderService::class)->approveOrder($record);
                        Notification::make()
                            ->title('Pesanan disetujui')
                            ->body("Pesanan {$record->order_code} berhasil disetujui.")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (Order $record) => $record->status === OrderStatus::SUBMITTED)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Order $record, array $data): void {
                        app(OrderService::class)->rejectOrder($record, $data['rejection_reason']);
                        Notification::make()
                            ->title('Pesanan ditolak')
                            ->body("Pesanan {$record->order_code} telah ditolak.")
                            ->warning()
                            ->send();
                    }),

                Action::make('view_order')
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record->id])),
            ]);
    }
}
