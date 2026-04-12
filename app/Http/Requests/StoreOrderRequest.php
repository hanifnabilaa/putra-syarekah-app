<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_method'       => 'required|in:shipping,pickup',
            'shipping_address'      => 'required_if:shipping_method,shipping|nullable|string|max:500',
            'notes'                 => 'nullable|string|max:1000',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => ['required', 'integer', 'min:10', $this->multiplesOfTenRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.quantity.multiple_of_10' => 'Jumlah pesanan harus kelipatan 10.',
            'shipping_address.required_if'    => 'Alamat pengiriman wajib diisi jika metode pengiriman dikirim.',
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
