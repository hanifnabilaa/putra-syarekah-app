<?php

namespace App\Filament\Gudang\Resources\WarehouseReceipts\Schemas;

use App\Enums\WarehouseReceiptStatus;
use App\Models\PrintingOrder;
use App\Models\WarehouseReceipt;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class WarehouseReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Penerimaan Info')
                            ->schema([
                                Select::make('printing_order_id')
                                    ->relationship(
                                        name: 'printingOrder',
                                        titleAttribute: 'order_code',
                                        modifyQueryUsing: fn (Builder $query, ?WarehouseReceipt $record) => $query->whereDoesntHave('warehouseReceipts', function (Builder $q) use ($record) {
                                            $q->where('status', WarehouseReceiptStatus::SELESAI_PRODUKSI);
                                            if ($record) {
                                                $q->where('id', '!=', $record->id);
                                            }
                                        })
                                    )
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if (! $state) {
                                            return;
                                        }
                                        $po = PrintingOrder::with('items')->find($state);
                                        if ($po) {
                                            $items = $po->items->map(fn ($item) => [
                                                'product_id' => $item->product_id,
                                                'qty_ordered' => $item->quantity ?? 0,
                                                'qty_received' => $item->quantity ?? 0,
                                            ])->toArray();
                                            $set('items', $items);
                                        }
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Printing Order (PO)'),
                                TextInput::make('receipt_code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => 'RCV-' . date('Ymd') . '-' . rand(100, 999))
                                    ->label('Receipt Code'),
                                Hidden::make('gudang_id')
                                    ->default(fn () => auth()->user()->getMasterId()),
                                Select::make('status')
                                    ->options(WarehouseReceiptStatus::class)
                                    ->default(WarehouseReceiptStatus::BELUM_PRODUKSI->value)
                                    ->required(),
                                DatePicker::make('receipt_date')
                                    ->default(now()),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Items (Penerimaan Aktual)')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_id')
                                            ->relationship('product', 'name')
                                            ->required()
                                            ->searchable(),
                                        TextInput::make('qty_ordered')
                                            ->numeric()
                                            ->required()
                                            ->label('Target Qty'),
                                        TextInput::make('qty_received')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->label('Aktual Qty'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(1)
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
