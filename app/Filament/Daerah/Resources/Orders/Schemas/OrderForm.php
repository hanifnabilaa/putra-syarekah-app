<?php

namespace App\Filament\Daerah\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Pesanan')
                            ->schema([
                                TextInput::make('order_code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => 'ORD-' . date('Ymd') . '-' . rand(100, 999)),
                                Hidden::make('daerah_id')
                                    ->default(fn () => auth()->id()),
                                Select::make('status')
                                    ->options(OrderStatus::class)
                                    ->default(OrderStatus::PENDING->value)
                                    ->required(),
                                DatePicker::make('order_date')
                                    ->default(now()),
                                Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Items')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_id')
                                            ->relationship('product', 'name')
                                            ->required()
                                            ->searchable(),
                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('price')
                                            ->numeric()
                                            ->required()
                                            ->default(0),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(1)
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
