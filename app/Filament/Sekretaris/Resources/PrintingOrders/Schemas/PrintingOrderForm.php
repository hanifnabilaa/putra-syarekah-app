<?php

namespace App\Filament\Sekretaris\Resources\PrintingOrders\Schemas;

use App\Enums\PrintingOrderStatus;
use App\Models\PrintingOrder;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrintingOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Order Info')
                            ->schema([
                                Select::make('percetakan_id')
                                    ->relationship('percetakan', 'name')
                                    ->required()
                                    ->label('Percetakan'),
                                TextInput::make('order_code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => 'PO-' . date('Ymd') . '-' . rand(100, 999))
                                    ->label('Order Code'),
                                Hidden::make('sekretaris_id')
                                    ->default(fn () => auth()->id()),
                                Select::make('status')
                                    ->options(function (?PrintingOrder $record): array {
                                        // If creating new record, only show DRAFT
                                        if (!$record || !$record->exists) {
                                            return [PrintingOrderStatus::DRAFT->value => PrintingOrderStatus::DRAFT->label()];
                                        }
                                        // For existing records, include current status and valid next transitions
                                        $options = [$record->status->value => $record->status->label()];
                                        foreach ($record->getAvailableTransitions() as $value => $label) {
                                            $options[$value] = $label;
                                        }
                                        return $options;
                                    })
                                    ->default(PrintingOrderStatus::DRAFT->value)
                                    ->required()
                                    ->hint(function (?PrintingOrder $record): ?string {
                                        if ($record && $record->exists) {
                                            return 'Status saat ini: ' . $record->status->label();
                                        }
                                        return null;
                                    }),
                                DatePicker::make('order_date')
                                    ->default(now())
                                    ->required(),
                                DatePicker::make('target_date'),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        // Product Summary Section - shows overview of all products
                        Section::make('Info Stok & Order')
                            ->description('Lihat stok gudang dan pesanan pending sebelum membuat PO')
                            ->headerActions([
                                // Placeholder for potential expand/collapse toggle
                            ])
                            ->schema([
                                Placeholder::make('product_summary')
                                    ->content(function (): \Illuminate\View\View {
                                        return view('components.product-summary-form');
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->collapsed(false),

                        Section::make('Items')
                            ->description('Tambahkan item pesanan percetakan')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_id')
                                            ->relationship('product', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                // Reset qty when product changes
                                                $set('qty', 1);
                                            })
                                            ->hint(function ($state) {
                                                if ($state) {
                                                    $product = Product::find($state);
                                                    if ($product) {
                                                        $pending = self::getPendingForProduct($product->id);
                                                        $suggested = max(0, $pending - $product->stock);
                                                        
                                                        $text = "Stok Gudang: {$product->stock} | Pesanan Pending: {$pending}";
                                                        if ($suggested > 0) {
                                                            $text .= " | PERLU DIPESAN: {$suggested}";
                                                        } else {
                                                            $text .= " | (Aman)";
                                                        }
                                                        
                                                        return $text;
                                                    }
                                                }
                                                return null;
                                            })
                                            ->hintColor(function ($state) {
                                                if ($state) {
                                                    $product = Product::find($state);
                                                    if ($product) {
                                                        $pending = self::getPendingForProduct($product->id);
                                                        $suggested = max(0, $pending - $product->stock);
                                                        
                                                        if ($suggested > 0) return 'danger';
                                                        if ($product->stock < 50) return 'warning';
                                                        return 'success';
                                                    }
                                                }
                                                return 'gray';
                                            }),
                                        TextInput::make('qty')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)
                                    ->addActionLabel('Tambah Item')
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    private static function getPendingForProduct(int $productId): int
    {
        return \App\Models\OrderItem::where('product_id', $productId)
            ->whereIn('order_id', function ($query) {
                $query->select('id')
                    ->from('orders')
                    ->where('status', \App\Enums\OrderStatus::APPROVED->value);
            })
            ->selectRaw('SUM(quantity - COALESCE(shipped_quantity, 0)) as pending')
            ->value('pending') ?? 0;
    }
}
