<div class="space-y-2">
    @if($product)
        <div class="grid grid-cols-2 gap-2 text-sm">
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="text-gray-500 text-xs">Stok Gudang</div>
                <div class="text-lg font-bold @if($product->stock < 50) text-danger @else text-success @endif">
                    {{ number_format($product->stock) }} unit
                </div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="text-gray-500 text-xs">Pending Order</div>
                <div class="text-lg font-bold text-warning">
                    {{ number_format($pendingInfo['pending'] ?? 0) }} unit
                </div>
            </div>
        </div>
        <div class="text-xs text-gray-500">
            <span class="font-medium">Suggested Order:</span>
            @php
                $suggested = max(0, ($pendingInfo['pending'] ?? 0) - $product->stock);
            @endphp
            @if($suggested > 0)
                <span class="text-danger font-bold">{{ number_format($suggested) }} unit</span>
            @else
                <span class="text-success">Stok cukup</span>
            @endif
        </div>
    @else
        <div class="text-sm text-gray-500 italic">
            Pilih produk untuk melihat informasi
        </div>
    @endif
</div>
