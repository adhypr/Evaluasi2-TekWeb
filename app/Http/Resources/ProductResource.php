<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Format data produk.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->product_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'brand' => $this->brand,
            'scale' => $this->scale,
            'condition' => $this->item_condition,
            'rating' => (float) $this->rating,
            'img_url' => $this->img ? url('storage/'.$this->img) : null,
            'category' => [
                'id' => $this->category_id,
                'name' => $this->category?->category,
            ],
            'category_name' => $this->category_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
