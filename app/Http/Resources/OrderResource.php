<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'order_code'       => $this->order_code,
            'status'           => $this->status->value,
            'status_label'     => $this->status->label(),
            'status_color'     => $this->status->color(),
            'shipping_method'  => $this->shipping_method->value,
            'method_label'     => $this->shipping_method->label(),
            'shipping_address' => $this->shipping_address,
            'total_bill'       => (float) $this->total_bill,
            'notes'            => $this->notes,
            'rejection_reason' => $this->rejection_reason,
            'daerah_id'        => $this->daerah_id,
            'items'            => OrderItemResource::collection($this->whenLoaded('items')),
            'shipments'        => ShipmentResource::collection($this->whenLoaded('shipments')),
            'bill'             => new BillResource($this->whenLoaded('bill')),
            'created_at'       => $this->created_at->format('d M Y'),
            'updated_at'       => $this->updated_at->format('d M Y H:i'),
        ];
    }
}
