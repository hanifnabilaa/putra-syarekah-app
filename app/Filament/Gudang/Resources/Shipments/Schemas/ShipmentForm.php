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
                                    ->options(function (?\App\Models\Shipment $record) {
                                        return Order::with(['daerah', 'bill', 'items'])
                                            ->where(function ($query) use ($record) {
                                                $query->where('status', \App\Enums\OrderStatus::APPROVED->value)
                                                    ->whereHas('items', function ($q) {
                                                        $q->whereRaw('COALESCE(shipped_quantity, 0) < quantity');
                                                    });

                                                if ($record && $record->order_id) {
                                                    $query->orWhere('id', $record->order_id);
                                                }
                                            })
                                            ->get()
                                            ->mapWithKeys(function ($o) {
                                                $statusPay = $o->bill?->status ? $o->bill->status->label() : 'Belum Ada Tagihan';
                                                $remainingItemsCount = $o->items->filter(fn ($item) => $item->remaining_quantity > 0)->count();
                                                return [$o->id => "{$o->order_code} — {$o->daerah->name} [Bayar: {$statusPay}] ({$remainingItemsCount} item belum dikirim)"];
                                            });
                                    })
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
                                    ->relationship()
                                    ->label('Item yang Dikirim')
                                    ->schema([
                                        Select::make('order_item_id')
                                            ->label('Produk')
                                            ->options(function (Get $get, ?\App\Models\ShipmentItem $record) {
                                                $orderId = $get('../../order_id');
                                                if (!$orderId) {
                                                    return [];
                                                }
                                                return OrderItem::where('order_id', $orderId)
                                                    ->with('product')
                                                    ->get()
                                                    ->filter(function ($item) use ($record) {
                                                        return $item->remaining_quantity > 0 || ($record && $record->order_item_id === $item->id);
                                                    })
                                                    ->mapWithKeys(fn ($item) => [
                                                        $item->id => "{$item->product->name} (Sisa Kirim: {$item->remaining_quantity} dari {$item->quantity})"
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
