<?php

namespace App\Filament\Percetakan\Resources;

use App\Filament\Percetakan\Resources\ProductResource\Pages;
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
    protected static ?string $navigationLabel = 'Manajemen Kitab';
    protected static ?string $modelLabel = 'Kitab';
    protected static ?string $pluralModelLabel = 'Kitab';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Informasi Kitab')->schema([
                TextInput::make('name')
                    ->label('Nama Kitab')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label('Foto Kitab')
                    ->image()
                    ->directory('kitab')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('4:3')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Harga per Eksemplar (Rp)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0),

                TextInput::make('stock')
                    ->label('Stok Awal')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->hiddenOn('edit'),

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
                    ->label('Nama Kitab')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
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
                \Filament\Actions\Action::make('add_stock')
                    ->label('Tambah Stok')
                    ->icon('heroicon-m-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('quantity')
                            ->label('Jumlah Masuk')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Textarea::make('notes')
                            ->label('Keterangan')
                            ->rows(2),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $record->increment('stock', $data['quantity']);
                        StockLog::create([
                            'product_id' => $record->id,
                            'user_id' => auth()->id(),
                            'type' => 'in',
                            'quantity' => $data['quantity'],
                            'notes' => $data['notes'] ?? null,
                        ]);
                        Notification::make()
                            ->title('Stok berhasil ditambahkan')
                            ->success()
                            ->send();
                    }),

                \Filament\Actions\Action::make('view_stock_log')
                    ->label('Riwayat Stok')
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
