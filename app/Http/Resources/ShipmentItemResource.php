<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'order_item_id' => $this->order_item_id,
            'order_item'    => new OrderItemResource($this->whenLoaded('orderItem')),
            'quantity'      => $this->quantity,
        ];
    }
}
