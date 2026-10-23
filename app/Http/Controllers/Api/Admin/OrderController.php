<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Semua order (terbaru di atas). Query: q (nomor/nama), status, per_page (maks 100).
     */
    public function index(Request $request)
    {
        $query = Order::with('items')->latest('id');

        if ($q = trim((string) $request->query('q'))) {
            $query->where(fn ($w) => $w
                ->where('order_number', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%"));
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);

        return OrderResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load('items'));
    }

    /**
     * Ubah status order. Membatalkan order mengembalikan stok; order yang sudah
     * dibatalkan tidak bisa diaktifkan lagi (stok sudah dikembalikan).
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $data = $request->validated();

        if ($order->status === 'cancelled' && $data['status'] !== 'cancelled') {
            throw ValidationException::withMessages([
                'status' => ['A cancelled order cannot be reactivated.'],
            ]);
        }

        DB::transaction(function () use ($order, $data) {
            if ($data['status'] === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items()->whereNotNull('product_id')->get() as $item) {
                    $item->product?->increment('stock', $item->qty);
                }
            }
            $order->update(['status' => $data['status']]);
        });

        return new OrderResource($order->refresh()->load('items'));
    }
}
