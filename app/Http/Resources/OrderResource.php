<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Bentuk JSON sengaja sama dengan objek order yang sudah dipakai komponen React
 * (OrdersTable, OrderDetailModal): { id, date, customer{...}, items[], subtotal, shipping, total }.
 */
class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shipping = config("dhyecast.shipping.{$this->shipping_method}");
        $payment = config("dhyecast.payments.{$this->payment_method}");

        return [
            'id' => $this->order_number,
            'date' => $this->created_at?->format('M j, Y, h:i A'),
            'created_at' => $this->created_at,
            'status' => $this->status,
            'customer' => [
                'name' => $this->customer_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'courier' => $shipping['label'] ?? $this->shipping_method,
                'paymentMethod' => $payment ?? $this->payment_method,
            ],
            'shipping_method' => $this->shipping_method,
            'payment_method' => $this->payment_method,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'price' => (float) $item->price,
                'qty' => $item->qty,
            ])->values()),
            'subtotal' => (float) $this->subtotal,
            'shipping' => (float) $this->shipping_cost,
            'total' => (float) $this->total,
        ];
    }
}
