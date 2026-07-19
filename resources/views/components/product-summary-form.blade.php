<div style="display: flex; flex-direction: column; gap: 1.5rem;">
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

    {{-- Summary Stats --}}
    <div style="display: flex; gap: 1rem; width: 100%;">
        <div style="flex: 1;">
            <x-filament::fieldset>
                <x-slot name="label">Stok Gudang</x-slot>
                <div style="font-size: 1.5rem; font-weight: bold; text-align: center;">
                    {{ number_format($totalStock) }}
                </div>
            </x-filament::fieldset>
        </div>
        <div style="flex: 1;">
            <x-filament::fieldset>
                <x-slot name="label">Pending Order</x-slot>
                <div style="font-size: 1.5rem; font-weight: bold; text-align: center; color: rgba(var(--warning-500), 1);">
                    {{ number_format($totalPending) }}
                </div>
            </x-filament::fieldset>
        </div>
        <div style="flex: 1;">
            <x-filament::fieldset>
                <x-slot name="label">Total Kekurangan</x-slot>
                <div style="font-size: 1.5rem; font-weight: bold; text-align: center; color: rgba(var(--{{ $totalKekurangan > 0 ? 'danger' : 'success' }}-500), 1);">
                    {{ number_format($totalKekurangan) }}
                </div>
            </x-filament::fieldset>
        </div>
    </div>

    {{-- Product List --}}
    @if($products->count() > 0)
        <div>
            <div style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; opacity: 0.8;">Detail per Produk</div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach($products as $p)
                    @php
                        $pending = max(0, ($p->order_items_sum_quantity ?? 0) - ($p->order_items_sum_shipped_quantity ?? 0));
                        $kekurangan = max(0, $pending - $p->stock);

                        if ($kekurangan > 0) {
                            $statusLabel = '+' . number_format($kekurangan) . ' perlu dipesan';
                            $statusColor = 'danger';
                        } elseif ($p->stock < 20) {
                            $statusLabel = 'Stok Kritis';
                            $statusColor = 'danger';
                        } elseif ($p->stock < 50) {
                            $statusLabel = 'Stok Rendah';
                            $statusColor = 'warning';
                        } else {
                            $statusLabel = 'Stok Aman';
                            $statusColor = 'success';
                        }
                    @endphp
                    <x-filament::fieldset style="padding: 0.75rem 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; flex-direction: column;">
                                <div style="font-weight: 600; font-size: 0.875rem;">{{ $p->name }}</div>
                                <div style="font-size: 0.75rem; margin-top: 0.1rem;">
                                    <span style="opacity: 0.7;">Stok Gudang:</span> 
                                    <span style="{{ $p->stock < 50 ? 'color: rgba(var(--danger-500), 1); font-weight: bold;' : 'font-weight: 500;' }}">{{ number_format($p->stock) }}</span>
                                    
                                    @if($pending > 0)
                                        <span style="opacity: 0.4; margin: 0 0.25rem;">|</span>
                                        <span style="opacity: 0.7;">Pesanan Daerah:</span> 
                                        <span style="color: rgba(var(--warning-500), 1); font-weight: bold;">{{ number_format($pending) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <x-filament::badge :color="$statusColor">
                                    {{ $statusLabel }}
                                </x-filament::badge>
                            </div>
                        </div>
                    </x-filament::fieldset>
                @endforeach
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 1.5rem; font-size: 0.875rem; opacity: 0.6;">
            Tidak ada data produk.
        </div>
    @endif
</div>
