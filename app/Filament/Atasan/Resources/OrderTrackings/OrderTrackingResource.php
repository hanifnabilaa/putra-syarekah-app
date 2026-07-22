<?php

namespace App\Filament\Atasan\Resources\OrderTrackings;

use App\Enums\OrderStatus;
use App\Filament\Atasan\Resources\OrderTrackings\Pages\ListOrderTrackings;
use App\Filament\Atasan\Resources\OrderTrackings\Pages\ViewOrderTracking;
use App\Filament\Atasan\Resources\OrderTrackings\Tables\OrderTrackingsTable;
use App\Models\Order;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class OrderTrackingResource extends Resource
{
    protected static ?string $model = Order::class;
    
    protected static ?string $navigationLabel = 'Tracking Pesanan';
    protected static ?string $modelLabel = 'Pesanan';
    protected static ?string $pluralModelLabel = 'Pesanan';
    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Info Pesanan & Daerah')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('order_code')->label('Kode Pesanan')->disabled(),
                        \Filament\Forms\Components\Select::make('daerah_id')
                            ->relationship('daerah', 'name')
                            ->label('Daerah Pemesan')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('created_at')->label('Tanggal Pesan')->disabled(),
                        \Filament\Forms\Components\TextInput::make('status')
                            ->label('Status Order')
                            ->formatStateUsing(fn ($state) => $state instanceof OrderStatus ? $state->label() : $state)
                            ->disabled(),
                        \Filament\Forms\Components\Textarea::make('shipping_address')->label('Alamat Pengiriman')->columnSpanFull()->disabled(),
                    ])
                    ->columns(4),

                \Filament\Schemas\Components\Section::make('Progress Keuangan (Tagihan)')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('bill.total_bill')
                            ->label('Total Tagihan')
                            ->prefix('Rp')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('bill.total_paid')
                            ->label('Total Dibayar')
                            ->prefix('Rp')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('bill.remaining_bill')
                            ->label('Sisa Tagihan')
                            ->prefix('Rp')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('bill.status')
                            ->label('Status Pembayaran')
                            ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\BillStatus ? $state->label() : ($state ?: 'Belum Ada'))
                            ->disabled(),
                    ])
                    ->columns(4),

                \Filament\Schemas\Components\Section::make('Progress Fulfillment (Pengiriman & Percetakan)')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->label('Detail Produk')
                            ->schema([
                                \Filament\Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->label('Produk')
                                    ->disabled(),
                                \Filament\Forms\Components\TextInput::make('quantity')->label('Total Dipesan')->disabled(),
                                \Filament\Forms\Components\TextInput::make('shipped_quantity')->label('Sudah Dikirim')->disabled(),
                                \Filament\Forms\Components\TextInput::make('remaining_quantity')
                                    ->label('Sisa Belum Dikirim')
                                    ->formatStateUsing(fn ($record) => $record ? ($record->quantity - ($record->shipped_quantity ?? 0)) : 0)
                                    ->disabled(),
                                \Filament\Forms\Components\Placeholder::make('stock')
                                    ->label('Stok Gudang Saat Ini')
                                    ->content(function ($record) {
                                        if (!$record) return '0';
                                        return \App\Models\ProductStock::where('product_id', $record->product_id)->sum('stock') ?? 0;
                                    }),
                                \Filament\Forms\Components\Placeholder::make('in_process')
                                    ->label('Sedang Dicetak (PO)')
                                    ->content(function ($record) {
                                        if (!$record) return '0';
                                        return \App\Models\PrintingOrderItem::where('product_id', $record->product_id)
                                            ->whereHas('printingOrder', fn ($q) => $q->where('status', '!=', \App\Enums\PrintingOrderStatus::SELESAI->value))
                                            ->sum('qty') ?? 0;
                                    }),
                            ])
                            ->columns(6)
                            ->disableItemCreation()
                            ->disableItemDeletion()
                            ->disableItemMovement()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return OrderTrackingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrderTrackings::route('/'),
            'view' => ViewOrderTracking::route('/{record}'),
        ];
    }
}
