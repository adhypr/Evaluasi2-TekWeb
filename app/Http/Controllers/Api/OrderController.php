<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest('id')->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($request->user()->isAdmin() || $order->user_id === $request->user()->id, 403);

        return new OrderResource($order->load('items'));
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $quantities = [];
        foreach ($data['items'] as $line) {
            $quantities[$line['product_id']] = ($quantities[$line['product_id']] ?? 0) + (int) $line['qty'];
        }

        $order = DB::transaction(function () use ($data, $quantities, $request) {
            $products = Product::whereIn('product_id', array_keys($quantities))
                ->orderBy('product_id')
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            $errors = [];
            foreach ($quantities as $productId => $qty) {
                $product = $products[$productId];
                if ($product->stock < $qty) {
                    $errors[] = "Insufficient stock for \"{$product->name}\" (available: {$product->stock}).";
                }
            }
            if ($errors) {
                throw ValidationException::withMessages(['items' => $errors]);
            }

            $subtotal = 0;
            foreach ($quantities as $productId => $qty) {
                $subtotal += (float) $products[$productId]->price * $qty;
            }
            $shippingCost = config("dhyecast.shipping.{$data['shipping']}.cost");

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $request->user('sanctum')?->id,
                'customer_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'shipping_method' => $data['shipping'],
                'payment_method' => $data['payment_method'],
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'status' => 'pending',
            ]);

            foreach ($quantities as $productId => $qty) {
                $product = $products[$productId];
                $order->items()->create([
                    'product_id' => $productId,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                ]);
                $product->decrement('stock', $qty);
            }

            return $order;
        });

        return new OrderResource($order->load('items'));
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.strtoupper(base_convert((string) now()->getTimestampMs(), 10, 36)).Str::upper(Str::random(2));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
