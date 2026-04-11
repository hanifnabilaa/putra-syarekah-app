<x-filament-panels::page>
    <div class="mb-4">
        <a href="{{ \App\Filament\Percetakan\Resources\ProductResource::getUrl('index') }}"
            class="text-sm text-primary-600 hover:underline">
            &larr; Kembali ke Daftar Produk
        </a>
    </div>

    <x-filament::section>
        <x-slot name="heading">Riwayat Stok: {{ $record->name }}</x-slot>
        <x-slot name="description">
            Stok saat ini: <strong>{{ $record->stock }}</strong> eksemplar
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>