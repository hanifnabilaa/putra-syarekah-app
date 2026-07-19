<x-filament-panels::page>
    <div class="mb-4">
        <a href="{{ \App\Filament\Gudang\Resources\ProductResource::getUrl('index') }}"
            class="text-sm text-primary-600 hover:underline">
            &larr; Kembali ke Daftar Produk
        </a>
    </div>

    <x-filament::section>
        <x-slot name="heading">Riwayat Stok: {{ $record->name }}</x-slot>
        <x-slot name="description">
            Stok Anda saat ini: <strong>{{ $record->productStocks()->where('gudang_id', auth()->id())->value('stock') ?? 0 }}</strong> eksemplar
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>