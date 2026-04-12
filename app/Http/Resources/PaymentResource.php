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
            'confirmed_at'     => $this->confirmed_at?->format('d M Y H:i'),
            'notes'            => $this->notes,
            'proof_image_url'  => $this->proof_image_url,
            'payment_date'     => $this->payment_date?->format('d M Y') ?? $this->created_at->format('d M Y'),
            'created_at'       => $this->created_at->format('d M Y'),
        ];
    }
}
