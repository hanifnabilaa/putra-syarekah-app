<?php

namespace App\Filament\Gudang\Resources\WarehouseReceipts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class WarehouseReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('receipt_code')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('printingOrder.order_code')
                    ->searchable()
                    ->sortable()
                    ->label('PO Code'),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('receipt_date')
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
