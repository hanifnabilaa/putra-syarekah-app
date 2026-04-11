<x-filament-panels::page>
<div style="max-width:860px;margin:0 auto;padding:0 1rem">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem">
        <a href="{{ \App\Filament\Percetakan\Resources\ShipmentResource::getUrl('index') }}"
           style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:12px;border:1px solid #374151;background:#1f2937;text-decoration:none;flex-shrink:0;transition:background .15s"
           onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#1f2937'"
           title="Kembali">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 style="font-size:1.375rem;font-weight:700;color:#f9fafb;margin:0;line-height:1.3">Buat Pengiriman Baru</h1>
            <p style="font-size:.8125rem;color:#6b7280;margin:.25rem 0 0">Pilih pesanan yang sudah disetujui lalu tentukan jumlah item yang dikirim.</p>
        </div>
    </div>

    <form wire:submit.prevent="submit">

        {{-- Filament Schema Form --}}
        {{ $this->form }}

        {{-- Item yang Dikirim --}}
        @if(count($orderItems) > 0)
        <div style="border-radius:16px;border:1px solid #374151;background:#111827;overflow:hidden;margin-top:1.25rem;box-shadow:0 4px 24px rgba(0,0,0,.3)">

            {{-- Section Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid #1f2937;background:rgba(255,255,255,.02)">
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#818cf8" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                    </div>
                    <div>
                        <p style="font-size:.9375rem;font-weight:600;color:#f3f4f6;margin:0">Item yang Dikirim</p>
                        <p style="font-size:.75rem;color:#6b7280;margin:.1rem 0 0">Atur jumlah per produk untuk sesi pengiriman ini</p>
                    </div>
                </div>
                <span style="display:inline-flex;align-items:center;gap:.375rem;font-size:.75rem;font-weight:600;color:#818cf8;background:rgba(99,102,241,.12);padding:.25rem .75rem;border-radius:100px;border:1px solid rgba(99,102,241,.2)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#818cf8" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/></svg>
                    {{ count($orderItems) }} produk
                </span>
            </div>

            {{-- Column Headers --}}
            <div style="display:grid;grid-template-columns:1fr auto;gap:1rem;padding:.625rem 1.5rem;background:rgba(255,255,255,.015);border-bottom:1px solid #1f2937">
                <span style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#6b7280">Produk</span>
                <span style="font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#6b7280;text-align:right;width:140px">Jumlah Kirim</span>
            </div>

            {{-- Items --}}
            @foreach($orderItems as $i => $item)
            @php
                $pct = $item['quantity'] > 0 ? round(($item['remaining_quantity'] / $item['quantity']) * 100) : 0;
                $isLast = $i === count($orderItems) - 1;
            @endphp
            <div style="display:flex;align-items:center;gap:1rem;padding:1rem 1.5rem;{{ !$isLast ? 'border-bottom:1px solid #1f2937;' : '' }}transition:background .1s"
                 onmouseover="this.style.background='rgba(255,255,255,.025)'" onmouseout="this.style.background='transparent'">

                {{-- Nomor --}}
                <div style="width:28px;height:28px;border-radius:8px;background:#1f2937;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#9ca3af;flex-shrink:0">
                    {{ $i + 1 }}
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0">
                    <p style="font-size:.9375rem;font-weight:600;color:#f9fafb;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $item['product_name'] }}
                    </p>
                    <div style="display:flex;align-items:center;gap:.5rem;margin-top:.375rem;flex-wrap:wrap">
                        <span style="font-size:.75rem;color:#9ca3af">Dipesan: <strong style="color:#d1d5db">{{ number_format($item['quantity']) }}</strong></span>
                        <span style="color:#374151;font-size:.75rem">·</span>
                        <span style="font-size:.75rem;color:#9ca3af">Terkirim: <strong style="color:#d1d5db">{{ number_format($item['shipped_quantity']) }}</strong></span>
                        <span style="color:#374151;font-size:.75rem">·</span>
                        <span style="font-size:.75rem;font-weight:600;color:#34d399">Sisa: {{ number_format($item['remaining_quantity']) }} eksp.</span>
                    </div>
                    {{-- Progress bar --}}
                    <div style="margin-top:.5rem;height:4px;background:#1f2937;border-radius:999px;overflow:hidden;width:180px">
                        <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#34d399,#059669);border-radius:999px;transition:width .3s"></div>
                    </div>
                </div>

                {{-- Input --}}
                <div style="flex-shrink:0;display:flex;align-items:center;gap:.5rem">
                    <div style="position:relative">
                        <input
                            type="number"
                            wire:model.lazy="quantities.{{ $item['id'] }}"
                            min="0"
                            max="{{ $item['remaining_quantity'] }}"
                            style="width:110px;padding:.5rem 2.25rem .5rem .875rem;border-radius:10px;border:1px solid #374151;background:#1f2937;color:#f9fafb;font-size:.9375rem;font-weight:600;text-align:right;outline:none;transition:border-color .15s,box-shadow .15s;-moz-appearance:textfield"
                            onfocus="this.style.borderColor='#818cf8';this.style.boxShadow='0 0 0 3px rgba(99,102,241,.15)'"
                            onblur="this.style.borderColor='#374151';this.style.boxShadow='none'"
                            placeholder="0"
                        />
                        <span style="position:absolute;right:.625rem;top:50%;transform:translateY(-50%);font-size:.6875rem;font-weight:500;color:#6b7280;pointer-events:none">eks</span>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Footer --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.875rem 1.5rem;background:rgba(99,102,241,.06);border-top:1px solid rgba(99,102,241,.15)">
                <span style="font-size:.8125rem;color:#9ca3af">Total yang akan dikirim sesi ini</span>
                <div style="display:flex;align-items:baseline;gap:.25rem">
                    <span style="font-size:1.25rem;font-weight:700;color:#818cf8">
                        {{ array_sum(array_intersect_key($quantities ?? [], array_flip(array_column($orderItems, 'id')))) }}
                    </span>
                    <span style="font-size:.8125rem;color:#818cf8;font-weight:500">eksemplar</span>
                </div>
            </div>
        </div>

        @elseif(filled($this->data['order_id'] ?? null))
        <div style="display:flex;align-items:center;gap:.875rem;padding:1rem 1.25rem;border-radius:12px;background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.2);margin-top:1.25rem">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(251,191,36,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fbbf24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            </div>
            <p style="font-size:.875rem;font-weight:500;color:#fcd34d;margin:0">
                Semua item pada pesanan ini sudah dikirim sepenuhnya.
            </p>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #1f2937">
            <a href="{{ \App\Filament\Percetakan\Resources\ShipmentResource::getUrl('index') }}"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.25rem;font-size:.875rem;font-weight:600;color:#9ca3af;background:#1f2937;border:1px solid #374151;border-radius:10px;text-decoration:none;transition:all .15s"
               onmouseover="this.style.background='#374151';this.style.color='#f9fafb'" onmouseout="this.style.background='#1f2937';this.style.color='#9ca3af'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                Batal
            </a>

            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:.625rem;padding:.625rem 1.75rem;font-size:.875rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#6366f1,#4f46e5);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 16px rgba(99,102,241,.4);transition:all .15s"
                    onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(99,102,241,.5)'"
                    onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 16px rgba(99,102,241,.4)'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                Buat Pengiriman
            </button>
        </div>

    </form>
</div>
</x-filament-panels::page>
