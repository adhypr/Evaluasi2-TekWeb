<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Order contoh (sama dengan seed order di frontend) agar dashboard admin tidak kosong.
     */
    public function run(): void
    {
        if (Order::where('order_number', 'ORD-777')->exists()) {
            return;
        }

        $order = Order::create([
            'order_number' => 'ORD-777',
            'customer_name' => 'Adhy',
            'email' => 'dhyadhy@gmail.com',
            'phone' => '081234567890',
            'address' => 'Jl. Dhyecast No. 1, Jakarta',
            'shipping_method' => 'standard',
            'payment_method' => 'bank_transfer',
            'subtotal' => 475000,
            'shipping_cost' => 25000,
            'total' => 500000,
            'status' => 'pending',
        ]);

        foreach ([[1, 2], [2, 1]] as [$productId, $qty]) {
            $product = Product::find($productId);
            if ($product) {
                $order->items()->create([
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                ]);
            }
        }
    }
}
