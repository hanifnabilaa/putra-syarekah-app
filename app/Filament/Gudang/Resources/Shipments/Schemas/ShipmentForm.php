<?php

namespace App\Filament\Gudang\Resources\Shipments\Schemas;

use App\Enums\ShipmentMethod;
use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Info Pengiriman')
                            ->schema([
                                Select::make('order_id')
                                    ->label('Pesanan Daerah')
                                    ->options(fn () => Order::with('daerah')
                                        ->get()
                                        ->mapWithKeys(fn ($o) => [$o->id => "{$o->order_code} — {$o->daerah->name}"]))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $set('items', []);
                                    }),

                                Select::make('status')
                                    ->options(ShipmentStatus::class)
                                    ->default(ShipmentStatus::PENDING->value)
                                    ->required(),

                                Select::make('method')
                                    ->options(ShipmentMethod::class)
                                    ->default(ShipmentMethod::SHIPPING->value)
                                    ->required(),

                                DatePicker::make('shipping_date')
                                    ->default(now()),

                                Textarea::make('shipping_address')
                                    ->columnSpanFull(),

                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Items Dikirim')
                            ->schema([
                                Repeater::make('items')
                                    ->label('Item yang Dikirim')
                                    ->schema([
                                        Select::make('order_item_id')
                                            ->label('Produk')
                                            ->options(function (Get $get) {
                                                $orderId = $get('../../order_id');
                                                if (!$orderId) {
                                                    return [];
                                                }
                                                return OrderItem::where('order_id', $orderId)
                                                    ->with('product')
                                                    ->get()
                                                    ->mapWithKeys(fn ($item) => [
                                                        $item->id => "{$item->product->name} (Qty: {$item->quantity})"
                                                    ]);
                                            })
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        TextInput::make('quantity')
                                            ->label('Jumlah Kirim')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)
                                    ->collapsible(),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
