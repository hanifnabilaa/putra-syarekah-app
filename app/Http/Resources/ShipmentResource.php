<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'queue_order'      => $this->queue_order,
            'status'           => $this->status->value,
            'status_label'     => $this->status->label(),
            'method'           => $this->method->value,
            'method_label'     => $this->method->label(),
            'shipping_address' => $this->shipping_address,
            'shipping_date'         => $this->shipping_date?->format('Y-m-d'),
            'notes'                 => $this->notes,
            'proof_of_delivery_url' => $this->proof_of_delivery ? asset('storage/' . $this->proof_of_delivery) : null,
            'items'                 => ShipmentItemResource::collection($this->whenLoaded('items')),
            'created_at'            => $this->created_at->format('d M Y'),
        ];
    }
}
