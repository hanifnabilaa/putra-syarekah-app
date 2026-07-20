<?php

namespace App\Filament\Gudang\Resources\ProductTransfers\Tables;

use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductTransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transfer_code')
                    ->label('Kode Transfer')
                    ->searchable(),
                TextColumn::make('fromGudang.name')
                    ->label('Pengirim')
                    ->searchable(),
                TextColumn::make('toGudang.name')
                    ->label('Penerima')
                    ->searchable(),
                TextColumn::make('transfer_date')
                    ->label('Tanggal Transfer')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
