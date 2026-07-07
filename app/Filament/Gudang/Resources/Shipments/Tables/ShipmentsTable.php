<?php

namespace App\Filament\Gudang\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('order.order_code')
                    ->searchable()
                    ->sortable()
                    ->label('Order Daerah'),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('method')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('shipping_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
