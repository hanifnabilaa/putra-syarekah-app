<?php

namespace App\Filament\Sekretaris\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ProductStockTable extends TableWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return 'Stok Gudang per Produk';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->active()
                    ->orderBy('stock', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok Gudang')
                    ->numeric()
                    ->sortable()
                    ->weight('bold')
                    ->color(fn (int $state): string => $state < 50 ? 'danger' : ($state < 100 ? 'warning' : 'success')),
                TextColumn::make('stock_status')
                    ->label('Status')
                    ->getStateUsing(function (Product $record): string {
                        if ($record->stock < 20) {
                            return 'Kritis';
                        } elseif ($record->stock < 50) {
                            return 'Rendah';
                        } elseif ($record->stock < 100) {
                            return 'Menengah';
                        }
                        return 'Aman';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Kritis' => 'danger',
                        'Rendah' => 'warning',
                        'Menengah' => 'info',
                        'Aman' => 'success',
                    }),
            ])
            ->filters([])
            ->emptyStateHeading('Tidak ada produk')
            ->emptyStateDescription('Belum ada produk aktif di katalog.');
    }
}
