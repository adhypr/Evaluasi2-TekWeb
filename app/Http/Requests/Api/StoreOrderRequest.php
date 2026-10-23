<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:1000',
            'shipping' => ['required', Rule::in(array_keys(config('dhyecast.shipping')))],
            'payment_method' => ['required', Rule::in(array_keys(config('dhyecast.payments')))],
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,product_id',
            'items.*.qty' => 'required|integer|min:1|max:99',
        ];
    }
}
