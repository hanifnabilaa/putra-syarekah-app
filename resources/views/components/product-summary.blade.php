<div class="space-y-3">
    @if($products && $products->count() > 0)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <h4 class="font-semibold text-amber-800 mb-2">Ringkasan Semua Produk</h4>
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div class="text-center p-2 bg-white rounded border">
                    <div class="text-gray-500 text-xs">Total Stok</div>
                    <div class="text-lg font-bold text-info">{{ number_format($totalStock) }}</div>
                </div>
                <div class="text-center p-2 bg-white rounded border">
                    <div class="text-gray-500 text-xs">Total Pending</div>
                    <div class="text-lg font-bold text-warning">{{ number_format($totalPending) }}</div>
                </div>
                <div class="text-center p-2 bg-white rounded border">
                    <div class="text-gray-500 text-xs">Total Kekurangan</div>
                    <div class="text-lg font-bold {{ $totalPending > $totalStock ? 'text-danger' : 'text-success' }}">
                        {{ number_format(max(0, $totalPending - $totalStock)) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 text-left">Produk</th>
                        <th class="px-2 py-1 text-right">Stok</th>
                        <th class="px-2 py-1 text-right">Diminta</th>
                        <th class="px-2 py-1 text-right">Pending</th>
                        <th class="px-2 py-1 text-right">Suggested</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                        @php
                            $pending = max(0, ($p->order_items_sum_quantity ?? 0) - ($p->order_items_sum_shipped_quantity ?? 0));
                            $suggested = max(0, $pending - $p->stock);
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-2 py-1">{{ $p->name }}</td>
                            <td class="px-2 py-1 text-right font-medium @if($p->stock < 50) text-danger @else text-gray-700 @endif">
                                {{ number_format($p->stock) }}
                            </td>
                            <td class="px-2 py-1 text-right text-info">{{ number_format($p->order_items_sum_quantity ?? 0) }}</td>
                            <td class="px-2 py-1 text-right text-warning font-medium">{{ number_format($pending) }}</td>
                            <td class="px-2 py-1 text-right @if($suggested > 0) text-danger font-bold @else text-success @endif">
                                {{ $suggested > 0 ? number_format($suggested) : '✓' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-sm text-gray-500 italic text-center py-4">
            Tidak ada data produk
        </div>
    @endif
</div>
