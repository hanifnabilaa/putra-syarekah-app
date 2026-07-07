<?php

namespace App\Filament\Percetakan\Resources\PrintingOrders\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Table;

class PrintingOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('order_code')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('sekretaris.name')
                    ->searchable()
                    ->sortable()
                    ->label('Sekretaris'),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('target_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
