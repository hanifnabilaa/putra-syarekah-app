<?php

namespace App\Filament\Sekretaris\Resources\PrintingOrders\Schemas;

use App\Enums\PrintingOrderStatus;
use App\Models\PrintingOrder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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
                                        // For existing records, show only valid next transitions
                                        return $record->getAvailableTransitions();
                                    })
                                    ->default(PrintingOrderStatus::DRAFT->value)
                                    ->required()
                                    ->hint(function (?PrintingOrder $record): ?string {
                                        if ($record && $record->exists && !empty($record->getAvailableTransitions())) {
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

                        Section::make('Items')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_id')
                                            ->relationship('product', 'name')
                                            ->required()
                                            ->searchable(),
                                        TextInput::make('qty')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1)
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
