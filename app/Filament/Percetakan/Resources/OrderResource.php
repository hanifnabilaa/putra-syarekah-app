<?php

namespace App\Filament\Percetakan\Resources;

use App\Enums\OrderStatus;
use App\Filament\Percetakan\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Manajemen Pesanan';
    protected static ?string $modelLabel = 'Pesanan';
    protected static ?string $pluralModelLabel = 'Pesanan';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Info Pesanan')->schema([
                TextInput::make('order_code')->label('Kode Pesanan')->disabled(),
                Select::make('daerah_id')
                    ->label('Daerah')
                    ->relationship('daerah', 'name')
                    ->searchable()
                    ->required(),
                Select::make('shipping_method')
                    ->label('Metode Pengiriman')
                    ->options(['shipping' => 'Dikirim ke Alamat', 'pickup' => 'Ambil Sendiri'])
                    ->required(),
                Textarea::make('shipping_address')
                    ->label('Alamat Pengiriman')
                    ->rows(2),
                Textarea::make('notes')->label('Catatan')->rows(2),
                TextInput::make('total_bill')
                    ->label('Total Tagihan')
                    ->prefix('Rp')
                    ->numeric()
                    ->disabled(),
            ])->columns(2),

            Section::make('Item Pesanan')->schema([
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->relationship('product', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, \Filament\Schemas\Set $set) {
                                $product = \App\Models\Product::find($state);
                                if ($product) {
                                    $set('unit_price', $product->price);
                                }
                            }),
                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->minValue(10)
                            ->step(10),
                        TextInput::make('unit_price')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])->columns(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Kode Pesanan')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('daerah.name')
                    ->label('Daerah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jml Produk')
                    ->counts('items'),

                Tables\Columns\TextColumn::make('total_bill')
                    ->label('Total')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('shipping_method')
                    ->label('Pengiriman')
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\ShipmentMethod ? $state->label() : $state),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof OrderStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof OrderStatus ? $state->color() : 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($e) => [$e->value => $e->label()])),

                Tables\Filters\SelectFilter::make('daerah_id')
                    ->label('Daerah')
                    ->relationship('daerah', 'name'),
            ])
            ->actions([
                \Filament\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === OrderStatus::SUBMITTED)
                    ->requiresConfirmation()
                    ->action(function (Order $record): void {
                        app(OrderService::class)->approveOrder($record);
                        Notification::make()->title('Pesanan disetujui')->success()->send();
                    }),

                \Filament\Actions\Action::make('reject')
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
                        Notification::make()->title('Pesanan ditolak')->warning()->send();
                    }),

                \Filament\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->visible(fn (Order $record) => $record->status === OrderStatus::APPROVED)
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pesanan?')
                    ->modalDescription('Pesanan yang sudah disetujui akan dibatalkan.')
                    ->action(function (Order $record): void {
                        app(OrderService::class)->cancelOrder($record);
                        Notification::make()->title('Pesanan dibatalkan')->warning()->send();
                    }),

                \Filament\Actions\EditAction::make()
                    ->visible(fn (Order $record) => $record->status === OrderStatus::APPROVED),

                \Filament\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
            'view'   => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
