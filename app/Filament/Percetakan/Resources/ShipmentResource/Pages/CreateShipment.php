<?php

namespace App\Filament\Percetakan\Resources\ShipmentResource\Pages;

use App\Filament\Percetakan\Resources\ShipmentResource;
use App\Models\OrderItem;
use App\Services\ShipmentService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class CreateShipment extends Page
{
    protected static string $resource = ShipmentResource::class;

    public ?int    $order_id          = null;
    public array   $selected_items    = [];
    public ?string $method            = 'shipping';
    public ?string $shipping_address  = null;
    public ?string $shipping_date     = null;
    public ?string $notes             = null;

    public function getTitle(): string
    {
        return 'Buat Pengiriman Baru';
    }

    public function getView(): string
    {
        return 'filament.percetakan.resources.shipment-resource.pages.create-shipment';
    }

    public function getOrderItems(): array
    {
        if (! $this->order_id) {
            return [];
        }

        return OrderItem::where('order_id', $this->order_id)
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
    }

    public function submit(): void
    {
        $this->validate([
            'order_id'       => 'required|exists:orders,id',
            'method'         => 'required|in:shipping,pickup',
            'shipping_date'  => 'nullable|date',
            'selected_items' => 'required|array|min:1',
            'selected_items.*.order_item_id' => 'required|exists:order_items,id',
            'selected_items.*.quantity'      => 'required|integer|min:1',
        ]);

        $order = \App\Models\Order::findOrFail($this->order_id);

        app(ShipmentService::class)->createShipment(
            $order,
            $this->selected_items,
            [
                'method'           => $this->method,
                'shipping_address' => $this->shipping_address,
                'shipping_date'    => $this->shipping_date,
                'notes'            => $this->notes,
            ]
        );

        Notification::make()->title('Pengiriman berhasil dibuat')->success()->send();
        $this->redirect(ShipmentResource::getUrl('index'));
    }
}
