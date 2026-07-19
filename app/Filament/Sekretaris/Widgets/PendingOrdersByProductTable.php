<?php

namespace App\Filament\Sekretaris\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingOrdersByProductTable extends TableWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return 'Pesanan Pending per Produk';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->selectRaw('products.*,
                        COALESCE(product_stocks.total_stock, 0) as total_stock,
                        COALESCE(order_items.total_ordered, 0) as total_ordered,
                        COALESCE(order_items.total_shipped, 0) as total_shipped,
                        COALESCE(order_items.total_ordered, 0) - COALESCE(order_items.total_shipped, 0) as pending_quantity,
                        COALESCE(po_items.total_in_process, 0) as total_in_process')
                    ->leftJoinSub(
                        \App\Models\ProductStock::query()
                            ->select('product_id')
                            ->selectRaw('SUM(stock) as total_stock')
                            ->groupBy('product_id'),
                        'product_stocks',
                        'products.id',
                        '=',
                        'product_stocks.product_id'
                    )
                    ->leftJoinSub(
                        OrderItem::query()
                            ->select('product_id')
                            ->selectRaw('SUM(quantity) as total_ordered, SUM(COALESCE(shipped_quantity, 0)) as total_shipped')
                            ->whereIn('order_id', function ($query) {
                                $query->select('id')
                                    ->from('orders')
                                    ->where('status', OrderStatus::APPROVED->value);
                            })
                            ->groupBy('product_id'),
                        'order_items',
                        'products.id',
                        '=',
                        'order_items.product_id'
                    )
                    ->leftJoinSub(
                        \App\Models\PrintingOrderItem::query()
                            ->select('product_id')
                            ->selectRaw('SUM(qty) as total_in_process')
                            ->whereIn('printing_order_id', function ($query) {
                                $query->select('id')
                                    ->from('printing_orders')
                                    ->where('status', '!=', \App\Enums\PrintingOrderStatus::SELESAI->value);
                            })
                            ->groupBy('product_id'),
                        'po_items',
                        'products.id',
                        '=',
                        'po_items.product_id'
                    )
                    ->havingRaw('(COALESCE(total_ordered, 0) - COALESCE(total_shipped, 0)) > 0 OR COALESCE(total_in_process, 0) > 0')
                    ->orderByRaw('(COALESCE(total_ordered, 0) - COALESCE(total_shipped, 0)) DESC')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_stock')
                    ->label('Stok Gudang')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => $state < 50 ? 'danger' : ($state < 100 ? 'warning' : 'success')),
                TextColumn::make('pending_quantity')
                    ->label('Kebutuhan Daerah')
                    ->numeric()
                    ->sortable()
                    ->color('warning')
                    ->weight('bold'),
                TextColumn::make('total_in_process')
                    ->label('Sedang Proses PO')
                    ->numeric()
                    ->sortable()
                    ->color('info')
                    ->weight('bold'),
                TextColumn::make('suggested_order')
                    ->label('Kekurangan (Perlu PO)')
                    ->numeric()
                    ->getStateUsing(function ($record): int {
                        // Net pending = needed by daerah - current stock - already ordered to printing (in process)
                        return max(0, ($record->pending_quantity ?? 0) - ($record->total_stock ?? 0) - ($record->total_in_process ?? 0));
                    })
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success'),
            ])
            ->filters([
                //
            ])
            ->emptyStateHeading('Tidak ada pesanan pending')
            ->emptyStateDescription('Semua pesanan daerah sudah terpenuhi atau belum ada pesanan.');
    }
}
