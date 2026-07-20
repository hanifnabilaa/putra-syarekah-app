<?php

namespace App\Filament\Gudang\Resources\ProductTransfers\Schemas;

use Filament\Schemas\Schema;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class ProductTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transfer')->schema([
                    Select::make('to_gudang_id')
                        ->label('Gudang Tujuan')
                        ->options(User::where('role', UserRole::GUDANG)->where('id', '!=', auth()->user()->getMasterId())->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    DatePicker::make('transfer_date')
                        ->label('Tanggal Transfer')
                        ->default(now())
                        ->required(),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make('Daftar Produk')->schema([
                    Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->label('Produk')
                                ->options(Product::pluck('name', 'id'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn ($state, callable $set) => $set('quantity', null)),
                            TextInput::make('quantity')
                                ->label('Kuantitas')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->rules([
                                    function (Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $productId = $get('product_id');
                                            if (!$productId) return;
                                            
                                            $stock = ProductStock::where('product_id', $productId)
                                                ->where('gudang_id', auth()->user()->getMasterId())
                                                ->value('stock') ?? 0;
                                                
                                            if ($value > $stock) {
                                                $fail("Kuantitas tidak boleh melebihi stok yang tersedia ({$stock}).");
                                            }
                                        };
                                    },
                                ]),
                        ])->columns(2)->minItems(1)
                ])
            ]);
    }
}
