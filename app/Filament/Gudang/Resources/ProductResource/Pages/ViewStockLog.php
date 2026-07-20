<?php

namespace App\Filament\Gudang\Resources\ProductResource\Pages;

use App\Filament\Gudang\Resources\ProductResource;
use App\Models\Product;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ViewStockLog extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProductResource::class;

    public Product $record;

    public function getTitle(): string
    {
        return "Riwayat Stok — {$this->record->name}";
    }

    public function getView(): string
    {
        return 'filament.gudang.resources.product-resource.pages.view-stock-log';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->record->stockLogs()->where('user_id', auth()->user()->getMasterId())->getQuery()->latest())
            ->columns([
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('type')->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'in' ? 'Masuk' : 'Keluar')
                    ->color(fn (string $state) => $state === 'in' ? 'success' : 'danger'),
                TextColumn::make('quantity')->label('Jumlah'),
                TextColumn::make('notes')->label('Keterangan')->limit(50),
                TextColumn::make('user.name')->label('Oleh'),
            ]);
    }
}
