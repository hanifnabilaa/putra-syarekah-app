<?php

namespace App\Filament\Sekretaris\Resources\Orders;

use App\Enums\OrderStatus;
use App\Filament\Sekretaris\Resources\Orders\Pages\ListOrders;
use App\Filament\Sekretaris\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Approval Pesanan Daerah';
    protected static ?string $modelLabel = 'Pesanan';
    protected static ?string $pluralModelLabel = 'Pesanan';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('Info Pesanan')->schema([
                    \Filament\Forms\Components\TextInput::make('order_code')
                        ->label('Kode Pesanan')
                        ->disabled(),
                    \Filament\Forms\Components\Select::make('daerah_id')
                        ->relationship('daerah', 'name')
                        ->label('Daerah')
                        ->disabled(),
                    \Filament\Forms\Components\Select::make('shipping_method')
                        ->label('Metode Pengiriman')
                        ->options(['shipping' => 'Dikirim ke Alamat', 'pickup' => 'Ambil Sendiri'])
                        ->disabled(),
                    \Filament\Forms\Components\Textarea::make('shipping_address')
                        ->label('Alamat Pengiriman')
                        ->rows(2)
                        ->disabled(),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->disabled(),
                    \Filament\Forms\Components\TextInput::make('total_bill')
                        ->label('Total Tagihan')
                        ->prefix('Rp')
                        ->numeric()
                        ->disabled(),
                ])->columns(2),

                \Filament\Schemas\Components\Section::make('Item Pesanan')->schema([
                    \Filament\Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            \Filament\Forms\Components\Select::make('product_id')
                                ->relationship('product', 'name')
                                ->label('Produk')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('quantity')
                                ->label('Jumlah')
                                ->numeric()
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('unit_price')
                                ->label('Harga Satuan')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled(),
                            \Filament\Forms\Components\TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled(),
                        ])->columns(4),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => \App\Filament\Sekretaris\Resources\Orders\Pages\ViewOrder::route('/{record}'),
        ];
    }
}
