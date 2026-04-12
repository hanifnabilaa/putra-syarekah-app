<?php

namespace App\Filament\Percetakan\Resources\ShipmentResource\Pages;

use App\Filament\Percetakan\Resources\ShipmentResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ShipmentService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class CreateShipment extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ShipmentResource::class;

    public ?array $data = [];

    /** Items list managed via Livewire (no Repeater) to avoid Filament namespace conflicts */
    public array $orderItems = [];
    public array $quantities = [];

    public function mount(): void
    {
        $this->form->fill([
            'method'        => 'shipping',
            'shipping_date' => now()->format('Y-m-d'),
        ]);
    }

    public function getTitle(): string
    {
        return 'Buat Pengiriman Baru';
    }

    public function getView(): string
    {
        return 'filament.percetakan.resources.shipment-resource.pages.create-shipment';
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Pesanan')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Select::make('order_id')
                            ->label('Pilih Pesanan (Sudah Disetujui)')
                            ->options(
                                Order::where('status', \App\Enums\OrderStatus::APPROVED)
                                    ->with('daerah')
                                    ->get()
                                    ->mapWithKeys(fn ($o) => [$o->id => "{$o->order_code} — {$o->daerah->name}"])
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (!$state) {
                                    $this->orderItems = [];
                                    $this->quantities = [];
                                    return;
                                }

                                $order = Order::find($state);
                                if ($order) {
                                    $set('shipping_address', $order->shipping_address);
                                    $set('method', $order->shipping_method?->value ?? 'shipping');

                                    $this->orderItems = OrderItem::where('order_id', $state)
                                        ->whereRaw('quantity > shipped_quantity')
                                        ->with('product')
                                        ->get()
                                        ->map(fn ($item) => [
                                            'id'                 => $item->id,
                                            'product_name'       => $item->product->name,
                                            'quantity'           => $item->quantity,
                                            'shipped_quantity'   => $item->shipped_quantity,
                                            'remaining_quantity' => $item->remaining_quantity,
                                        ])
                                        ->toArray();

                                    $this->quantities = collect($this->orderItems)
                                        ->mapWithKeys(fn ($item) => [$item['id'] => $item['remaining_quantity']])
                                        ->toArray();
                                }
                            }),
                    ]),

                Section::make('Detail Pengiriman')
                    ->icon('heroicon-o-truck')
                    ->visible(fn (Get $get) => filled($get('order_id')))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('method')
                                    ->label('Metode Pengiriman')
                                    ->options([
                                        'shipping' => 'Dikirim ke Alamat',
                                        'pickup'   => 'Ambil Sendiri',
                                    ])
                                    ->required(),

                                DatePicker::make('shipping_date')
                                    ->label('Tanggal Pengiriman')
                                    ->required(),

                                Textarea::make('shipping_address')
                                    ->label('Alamat Pengiriman')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Textarea::make('notes')
                                    ->label('Keterangan / Catatan')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $formData = $this->form->getState();

        $items = collect($this->orderItems)
            ->filter(fn ($item) => (int) ($this->quantities[$item['id']] ?? 0) > 0)
            ->map(fn ($item) => [
                'order_item_id' => $item['id'],
                'quantity'      => (int) ($this->quantities[$item['id']] ?? 0),
            ])
            ->values()
            ->toArray();

        if (empty($items)) {
            Notification::make()
                ->title('Gagal')
                ->body('Pilih setidaknya satu item dengan jumlah lebih dari 0 untuk dikirim.')
                ->danger()
                ->send();
            return;
        }

        $order = Order::findOrFail($formData['order_id']);

        app(ShipmentService::class)->createShipment(
            $order,
            $items,
            [
                'method'           => $formData['method'],
                'shipping_address' => $formData['shipping_address'] ?? null,
                'shipping_date'    => $formData['shipping_date'],
                'notes'            => $formData['notes'] ?? null,
            ]
        );

        Notification::make()
            ->title('Berhasil')
            ->body('Pengiriman baru telah berhasil dibuat.')
            ->success()
            ->send();

        $this->redirect(ShipmentResource::getUrl('index'));
    }
}
