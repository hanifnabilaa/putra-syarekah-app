<?php

namespace App\Filament\Atasan\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Order::query()->latest()->limit(5)
            )
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('order_code')
                    ->label('Kode Order')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                \Filament\Tables\Columns\TextColumn::make('daerah.name')
                    ->label('Daerah')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('total_bill')
                    ->label('Total Tagihan')
                    ->money('idr')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
