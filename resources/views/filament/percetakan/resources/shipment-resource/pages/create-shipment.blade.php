<x-filament-panels::page>
    <div class="mb-4">
        <a href="{{ \App\Filament\Percetakan\Resources\ShipmentResource::getUrl('index') }}"
           class="text-sm text-primary-600 hover:underline">
            &larr; Kembali ke Daftar Pengiriman
        </a>
    </div>

    <x-filament-panels::section>
        <x-slot name="heading">Buat Pengiriman Baru</x-slot>

        <div class="space-y-6">
            {{-- Pilih Pesanan --}}
            <div>
                <label class="block text-sm font-medium mb-1">Pesanan (yang sudah disetujui)</label>
                <select wire:model.live="order_id" class="w-full border rounded p-2">
                    <option value="">-- Pilih Pesanan --</option>
                    @foreach(\App\Models\Order::where('status', 'approved')->with('daerah')->get() as $order)
                        <option value="{{ $order->id }}">{{ $order->order_code }} — {{ $order->daerah->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($order_id)
            {{-- Item-item yang bisa dikirim --}}
            <div>
                <label class="block text-sm font-medium mb-2">Pilih Item yang Dikirim</label>
                @foreach($this->getOrderItems() as $i => $item)
                <div class="flex items-center gap-4 p-3 border rounded mb-2">
                    <div class="flex-1">
                        <p class="font-medium">{{ $item['product_name'] }}</p>
                        <p class="text-sm text-gray-500">Dipesan: {{ $item['quantity'] }} | Sudah dikirim: {{ $item['shipped_quantity'] }} | Sisa: {{ $item['remaining_quantity'] }}</p>
                    </div>
                    <div class="w-32">
                        <input type="number"
                               wire:model="selected_items.{{ $i }}.quantity"
                               max="{{ $item['remaining_quantity'] }}"
                               min="0"
                               class="w-full border rounded p-1"
                               placeholder="Jml kirim" />
                        <input type="hidden" wire:model="selected_items.{{ $i }}.order_item_id" value="{{ $item['id'] }}" />
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Detail Pengiriman --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Metode</label>
                    <select wire:model="method" class="w-full border rounded p-2">
                        <option value="shipping">Dikirim ke Alamat</option>
                        <option value="pickup">Ambil Sendiri</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Kirim</label>
                    <input type="date" wire:model="shipping_date" class="w-full border rounded p-2" />
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Alamat Pengiriman</label>
                    <textarea wire:model="shipping_address" rows="2" class="w-full border rounded p-2"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Keterangan</label>
                    <textarea wire:model="notes" rows="2" class="w-full border rounded p-2"></textarea>
                </div>
            </div>

            <div>
                <x-filament::button wire:click="submit" color="success">
                    Buat Pengiriman
                </x-filament::button>
            </div>
            @endif
        </div>
    </x-filament-panels::section>
</x-filament-panels::page>
