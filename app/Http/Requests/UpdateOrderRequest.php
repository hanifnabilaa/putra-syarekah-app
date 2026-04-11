<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_method'       => 'sometimes|in:shipping,pickup',
            'shipping_address'      => 'nullable|string|max:500',
            'notes'                 => 'nullable|string|max:1000',
            'items'                 => 'sometimes|array|min:1',
            'items.*.product_id'    => 'required_with:items|exists:products,id',
            'items.*.quantity'      => ['required_with:items', 'integer', 'min:10', $this->multiplesOfTenRule()],
        ];
    }

    private function multiplesOfTenRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if ($value % 10 !== 0) {
                $fail('Jumlah pesanan harus kelipatan 10 (10, 20, 30, ...).');
            }
        };
    }
}
