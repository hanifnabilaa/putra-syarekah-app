<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $totalBill     = (float) $this->total_bill;
        $totalPaid     = (float) $this->total_paid;
        $payPercent    = $totalBill > 0 ? round(($totalPaid / $totalBill) * 100, 1) : 0;

        return [
            'id'              => $this->id,
            'order_id'        => $this->order_id,
            'order'           => new OrderResource($this->whenLoaded('order')),
            'total_bill'      => $totalBill,
            'total_paid'      => $totalPaid,
            'remaining_bill'  => (float) $this->remaining_bill,
            'payment_percent' => $payPercent,
            'status'          => $this->status->value,
            'status_label'    => $this->status->label(),
            'status_color'    => $this->status->color(),
            'payments'        => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at'      => $this->created_at->format('d M Y'),
        ];
    }
}
