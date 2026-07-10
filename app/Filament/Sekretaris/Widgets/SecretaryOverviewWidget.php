<?php

namespace App\Filament\Sekretaris\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SecretaryOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Count pending orders from daerah (APPROVED = approved and waiting for fulfillment)
        $pendingOrdersCount = Order::where('status', OrderStatus::APPROVED)->count();

        // Calculate total items pending fulfillment from APPROVED orders
        $pendingItemsCount = OrderItem::whereIn('order_id', function ($query) {
            $query->select('id')
                ->from('orders')
                ->where('status', OrderStatus::APPROVED);
        })
            ->selectRaw('SUM(quantity - COALESCE(shipped_quantity, 0)) as pending')
            ->value('pending') ?? 0;

        // Total products in catalog
        $totalProducts = Product::active()->count();

        // Products with low stock (less than 50)
        $lowStockProducts = Product::active()->where('stock', '<', 50)->count();

        return [
            Stat::make('Pesanan Pending', $pendingOrdersCount)
                ->description('Order daerah yang menunggu fulfillment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Item Pending', (int) $pendingItemsCount)
                ->description('Total unit yang belum terpenuhi')
                ->descriptionIcon('heroicon-m-queue-list')
                ->color('warning'),
            Stat::make('Total Produk', $totalProducts)
                ->description('Produk aktif di katalog')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('info'),
            Stat::make('Stok Rendah', $lowStockProducts)
                ->description('Produk dengan stok < 50 unit')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'danger' : 'success'),
        ];
    }
}
