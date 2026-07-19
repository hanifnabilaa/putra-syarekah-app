<?php

namespace App\Filament\Percetakan\Resources\PrintingOrders\Schemas;

use App\Enums\PrintingOrderStatus;
use Filament\Forms\Components\DatePicker;
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
                                    ->disabled()
                                    ->label('Percetakan'),
                                TextInput::make('order_code')
                                    ->disabled()
                                    ->label('Order Code'),
                                Select::make('status')
                                    ->options(function (?\App\Models\PrintingOrder $record): array {
                                        if (!$record || !$record->exists) {
                                            return [\App\Enums\PrintingOrderStatus::DRAFT->value => \App\Enums\PrintingOrderStatus::DRAFT->label()];
                                        }
                                        $options = [$record->status->value => $record->status->label()];
                                        foreach ($record->getAvailableTransitions() as $value => $label) {
                                            $options[$value] = $label;
                                        }
                                        return $options;
                                    })
                                    ->required(),
                                DatePicker::make('order_date')
                                    ->disabled(),
                                DatePicker::make('target_date')
                                    ->disabled(),
                                Textarea::make('notes')
                                    ->disabled()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Items')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship()
                                    ->addable(false)
                                    ->deletable(false)
                                    ->schema([
                                        Select::make('product_id')
                                            ->relationship('product', 'name')
                                            ->disabled(),
                                        TextInput::make('qty')
                                            ->numeric()
                                            ->disabled(),
                                    ])
                                    ->columns(2)
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
