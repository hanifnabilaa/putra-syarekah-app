<div class="space-y-4">
    @php
        $products = \App\Models\Product::active()
            ->withSum([
                'orderItems' => function ($query) {
                    $query->whereIn('order_id', function ($sub) {
                        $sub->select('id')
                            ->from('orders')
                            ->where('status', \App\Enums\OrderStatus::APPROVED->value);
                    });
                }
            ], 'quantity')
            ->withSum([
                'orderItems' => function ($query) {
                    $query->whereIn('order_id', function ($sub) {
                        $sub->select('id')
                            ->from('orders')
                            ->where('status', \App\Enums\OrderStatus::APPROVED->value);
                    });
                }
            ], 'shipped_quantity')
            ->get();

        $totalStock = $products->sum('stock');
        $totalPending = $products->sum(function ($p) {
            return max(0, ($p->order_items_sum_quantity ?? 0) - ($p->order_items_sum_shipped_quantity ?? 0));
        });
        $totalKekurangan = max(0, $totalPending - $totalStock);
    @endphp

    @if($products->count() > 0)
        {{-- Summary Cards --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-center">
                <div class="text-xs text-blue-600 font-medium">Total Stok Gudang</div>
                <div class="text-xl font-bold text-blue-700">{{ number_format($totalStock) }}</div>
                <div class="text-xs text-blue-500">unit tersedia</div>
            </div>
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-center">
                <div class="text-xs text-amber-600 font-medium">Total Pending Order</div>
                <div class="text-xl font-bold text-amber-700">{{ number_format($totalPending) }}</div>
                <div class="text-xs text-amber-500">diminta daerah</div>
            </div>
            <div class="p-3 {{ $totalKekurangan > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }} rounded-lg text-center">
                <div class="text-xs {{ $totalKekurangan > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">Kekurangan</div>
                <div class="text-xl font-bold {{ $totalKekurangan > 0 ? 'text-red-700' : 'text-green-700' }}">
                    {{ number_format($totalKekurangan) }}
                </div>
                <div class="text-xs {{ $totalKekurangan > 0 ? 'text-red-500' : 'text-green-500' }}">
                    {{ $totalKekurangan > 0 ? 'unit perlu dipesan' : 'stok aman' }}
                </div>
            </div>
        </div>

        {{-- Product Detail Table --}}
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Diminta</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($products as $p)
                        @php
                            $pending = max(0, ($p->order_items_sum_quantity ?? 0) - ($p->order_items_sum_shipped_quantity ?? 0));
                            $kekurangan = max(0, $pending - $p->stock);

                            if ($p->stock < 20) {
                                $status = 'Kritis';
                                $statusColor = 'danger';
                            } elseif ($p->stock < 50) {
                                $status = 'Rendah';
                                $statusColor = 'warning';
                            } elseif ($kekurangan > 0) {
                                $status = 'Perlu PO';
                                $statusColor = 'warning';
                            } else {
                                $status = 'Aman';
                                $statusColor = 'success';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium text-gray-900">{{ $p->name }}</td>
                            <td class="px-3 py-2 text-right {{ $p->stock < 50 ? 'text-danger font-semibold' : 'text-gray-700' }}">
                                {{ number_format($p->stock) }}
                            </td>
                            <td class="px-3 py-2 text-right text-blue-600">
                                {{ number_format($p->order_items_sum_quantity ?? 0) }}
                            </td>
                            <td class="px-3 py-2 text-right text-amber-600 font-medium">
                                {{ number_format($pending) }}
                            </td>
                            <td class="px-3 py-2 text-right">
                                @if($status === 'Kritis')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Kritis ({{ number_format($kekurangan) }})
                                    </span>
                                @elseif($status === 'Rendah')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Rendah
                                    </span>
                                @elseif($status === 'Perlu PO')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                        +{{ number_format($kekurangan) }} perlu
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Aman ✓
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <p class="text-sm italic">Tidak ada produk aktif di katalog.</p>
        </div>
    @endif
</div>
