<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    /**
     * Ringkasan untuk AdminDashboard. Order yang dibatalkan tidak dihitung sebagai pendapatan.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'total_revenue' => (float) Order::where('status', '!=', 'cancelled')->sum('total'),
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock', '<=', config('dhyecast.low_stock_threshold'))->count(),
            'orders_by_status' => Order::selectRaw('status, count(*) as cnt')
                ->groupBy('status')
                ->pluck('cnt', 'status')
                ->map(fn ($n) => (int) $n),
        ]);
    }
}
