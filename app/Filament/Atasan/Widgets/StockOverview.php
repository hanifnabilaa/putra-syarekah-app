<?php

namespace App\Filament\Atasan\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPO = \App\Models\PrintingOrder::count();
        $totalOrders = \App\Models\Order::count();
        $totalRevenue = \App\Models\Order::sum('total_bill');
        $totalShipments = \App\Models\Shipment::count();

        $totalStokMasuk = \App\Models\StockLog::where('type', 'in')->sum('quantity');

        return [
            Stat::make('Total Pesanan Cetak (PO)', $totalPO)
                ->description('Total order cetak ke percetakan')
                ->descriptionIcon('heroicon-m-printer')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Pesanan Masuk (Daerah)', $totalOrders)
                ->description('Total pesanan masuk dari daerah')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([3, 12, 5, 8, 3, 10, 14])
                ->color('info'),
            Stat::make('Total Stok Masuk (Gudang)', $totalStokMasuk)
                ->description('Total kuantitas barang masuk ke gudang')
                ->descriptionIcon('heroicon-m-cube')
                ->chart([2, 5, 12, 8, 14, 10, 20])
                ->color('primary'),
            Stat::make('Total Nilai Transaksi', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total tagihan dari semua order')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([15, 4, 2, 12, 4, 10, 16])
                ->color('warning'),
        ];
    }
}
