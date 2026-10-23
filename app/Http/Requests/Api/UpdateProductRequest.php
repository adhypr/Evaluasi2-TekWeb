<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:categories,category_id',
            'category_name' => 'sometimes|nullable|string|max:150',
            'brand' => 'nullable|string|max:100',
            'scale' => 'nullable|string|max:20',
            'condition' => 'nullable|string|max:30',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'rating' => 'nullable|numeric|between:0,5',
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
