<?php

namespace App\Filament\Atasan\Resources\GudangTrackings;

use App\Filament\Atasan\Resources\GudangTrackings\Pages\ListGudangTrackings;
use App\Filament\Atasan\Resources\GudangTrackings\Pages\ViewGudangTracking;
use App\Models\Gudang;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\IconColumn;

class GudangTrackingResource extends Resource
{
    protected static ?string $model = Gudang::class;
    
    protected static ?string $navigationLabel = 'Tracking Gudang';
    protected static ?string $modelLabel = 'Gudang';
    protected static ?string $pluralModelLabel = 'Gudang';
    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Info Gudang & PIC')
                    ->schema([
                        TextInput::make('name')->label('Nama Gudang')->disabled(),
                        TextInput::make('pic_name')->label('Nama PIC')->disabled(),
                        TextInput::make('pic_phone')->label('Telepon PIC')->disabled(),
                        Toggle::make('is_active')->label('Status Aktif')->disabled(),
                        Textarea::make('address')->label('Alamat')->columnSpanFull()->disabled(),
                    ])
                    ->columns(4),

                Section::make('Ketersediaan Stok Produk')
                    ->description('Daftar stok barang aktual yang tersimpan di gudang ini.')
                    ->schema([
                        Repeater::make('productStocks')
                            ->relationship('productStocks')
                            ->label('Stok per Produk')
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->label('Produk')
                                    ->disabled(),
                                TextInput::make('stock')
                                    ->label('Jumlah Stok')
                                    ->numeric()
                                    ->disabled(),
                            ])
                            ->columns(2)
                            ->disableItemCreation()
                            ->disableItemDeletion()
                            ->disableItemMovement()
                    ]),

                Section::make('Riwayat Aktivitas (Keluar / Masuk Barang)')
                    ->description('Log riwayat pergerakan stok (Barang Masuk dari percetakan / Barang Keluar untuk pengiriman).')
                    ->schema([
                        Repeater::make('stockLogs')
                            ->relationship('stockLogs')
                            ->label('Riwayat Stok')
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->label('Produk')
                                    ->disabled(),
                                TextInput::make('type')
                                    ->label('Tipe')
                                    ->formatStateUsing(fn ($state) => $state === 'in' ? 'Barang Masuk' : 'Barang Keluar')
                                    ->disabled(),
                                TextInput::make('quantity')
                                    ->label('Kuantitas')
                                    ->disabled(),
                                TextInput::make('created_at')
                                    ->label('Waktu')
                                    ->disabled(),
                                TextInput::make('notes')
                                    ->label('Keterangan')
                                    ->columnSpanFull()
                                    ->disabled(),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->disableItemCreation()
                            ->disableItemDeletion()
                            ->disableItemMovement()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Gudang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pic_name')
                    ->label('Nama PIC')
                    ->searchable(),

                TextColumn::make('pic_phone')
                    ->label('Telepon PIC')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),

                TextColumn::make('total_products')
                    ->label('Macam Produk')
                    ->getStateUsing(function (Gudang $record) {
                        return $record->productStocks()->count() . ' produk';
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_stock')
                    ->label('Total Stok Gabungan')
                    ->getStateUsing(function (Gudang $record) {
                        return $record->productStocks()->sum('stock') . ' pcs';
                    })
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGudangTrackings::route('/'),
            'view' => ViewGudangTracking::route('/{record}'),
        ];
    }
}
