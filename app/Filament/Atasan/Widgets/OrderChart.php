<?php

namespace App\Filament\Atasan\Widgets;

use Filament\Widgets\ChartWidget;

class OrderChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Tren Transaksi Bulanan';
    protected int | string | array $columnSpan = 'full';
    
    protected function getData(): array
    {
        $data = \App\Models\Order::select(
            \Illuminate\Support\Facades\DB::raw('MONTH(created_at) as month'),
            \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'),
        )
            ->whereYear('created_at', now()->year)
            ->groupBy(\Illuminate\Support\Facades\DB::raw('MONTH(created_at)'))
            ->orderBy(\Illuminate\Support\Facades\DB::raw('MONTH(created_at)'))
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $dataset = [];
        $labels = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($i = 1; $i <= 12; $i++) {
            $dataset[] = $data[$i] ?? 0;
            $labels[] = $months[$i - 1];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pesanan (Order)',
                    'data' => $dataset,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
