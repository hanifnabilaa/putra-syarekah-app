<?php

namespace App\Filament\Sekretaris\Resources;

use App\Filament\Sekretaris\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\StockLog;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $modelLabel = 'Produk';
    protected static ?string $pluralModelLabel = 'Produk';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Informasi Produk')->schema([
                TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Foto Produk')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('Produk')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('4:3')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Harga per Eksemplar (Rp)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->disk('public')
                    ->height(50)
                    ->width(70),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Total Stok')
                    ->badge()
                    ->color(fn(int $state): string => $state > 0 ? 'success' : 'danger'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                \Filament\Actions\Action::make('view_stock_log')
                    ->label('Riwayat Stok Keseluruhan')
                    ->icon('heroicon-m-clock')
                    ->color('info')
                    ->url(fn(Product $record): string => static::getUrl('stock-log', ['record' => $record])),

                \Filament\Actions\EditAction::make(),

                \Filament\Actions\Action::make('toggle_active')
                    ->label(fn(Product $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn(Product $record) => $record->is_active ? 'heroicon-m-eye-slash' : 'heroicon-m-eye')
                    ->color(fn(Product $record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn(Product $record) => $record->update(['is_active' => !$record->is_active])),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'stock-log' => Pages\ViewStockLog::route('/{record}/stock-log'),
        ];
    }
}
