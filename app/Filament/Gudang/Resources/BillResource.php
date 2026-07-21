<?php

namespace App\Filament\Gudang\Resources;

use App\Enums\BillStatus;
use App\Filament\Gudang\Resources\BillResource\Pages;
use App\Filament\Gudang\Resources\BillResource\RelationManagers;
use App\Models\Bill;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Cek Pembayaran';

    protected static ?string $modelLabel = 'Status Pembayaran Tagihan';

    protected static ?string $pluralModelLabel = 'Status Pembayaran Tagihan';

    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Info Tagihan & Status Pembayaran')->schema([
                TextInput::make('order_code')
                    ->label('Kode Pesanan')
                    ->formatStateUsing(fn ($record) => $record?->order?->order_code)
                    ->disabled(),

                TextInput::make('daerah_name')
                    ->label('Daerah Pemesan')
                    ->formatStateUsing(fn ($record) => $record?->order?->daerah?->name)
                    ->disabled(),

                TextInput::make('total_bill')
                    ->label('Total Tagihan')
                    ->prefix('Rp')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 0, ',', '.'))
                    ->disabled(),

                TextInput::make('total_paid')
                    ->label('Total Dibayar')
                    ->prefix('Rp')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 0, ',', '.'))
                    ->disabled(),

                TextInput::make('remaining_bill')
                    ->label('Sisa Tagihan')
                    ->prefix('Rp')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 0, ',', '.'))
                    ->disabled(),

                TextInput::make('status')
                    ->label('Status Pembayaran')
                    ->formatStateUsing(fn ($state) => $state instanceof BillStatus ? $state->label() : (is_string($state) ? ucfirst($state) : $state))
                    ->disabled(),

                TextInput::make('order_status')
                    ->label('Status Pesanan')
                    ->formatStateUsing(fn ($record) => $record?->order?->status?->label() ?? '-')
                    ->disabled(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_code')
                    ->label('Kode Pesanan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.daerah.name')
                    ->label('Daerah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_bill')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Dibayar')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining_bill')
                    ->label('Sisa Tagihan')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state) => (float) $state > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof BillStatus ? $state->label() : (is_string($state) ? ucfirst($state) : $state))
                    ->color(fn ($state) => $state instanceof BillStatus ? $state->color() : 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.status')
                    ->label('Status Pesanan')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options(collect(BillStatus::cases())->mapWithKeys(fn ($e) => [$e->value => $e->label()])),

                Tables\Filters\SelectFilter::make('daerah')
                    ->label('Daerah')
                    ->relationship('order.daerah', 'name'),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'view' => Pages\ViewBill::route('/{record}'),
        ];
    }
}
