<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'amount'         => (float) $this->amount,
            'confirmed_by'   => $this->confirmedBy?->name,
            'confirmed_at'   => $this->confirmed_at?->format('d M Y H:i'),
            'notes'          => $this->notes,
            'created_at'     => $this->created_at->format('d M Y'),
        ];
    }
}
