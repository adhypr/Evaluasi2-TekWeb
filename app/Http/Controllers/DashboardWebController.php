<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardWebController extends Controller
{
    /**
     * Tampilan Dashboard Backoffice Blade (Identik 1:1 dengan AdminDashboard.jsx).
     */
    public function index(Request $request)
    {
        $title = "Dashboard Overview";

        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();

        $query = Order::with('items')->latest('id');

        if ($request->filled('s')) {
            $keyword = trim($request->input('s'));
            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'like', "%{$keyword}%")
                  ->orWhere('customer_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->paginate(6)->withQueryString();

        return view('admin.dashboard', compact(
            'title',
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCategories',
            'orders'
        ));
    }

    /**
     * Update status pesanan dari Modal Dashboard Blade (dengan rollback stok jika cancelled).
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,paid,shipped,completed,cancelled'],
        ]);

        if ($order->status === 'cancelled' && $validated['status'] !== 'cancelled') {
            return back()->with('error', "Pesanan #{$order->order_number} yang sudah dibatalkan tidak dapat diaktifkan kembali.");
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $validated) {
            if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items()->whereNotNull('product_id')->get() as $item) {
                    $item->product?->increment('stock', $item->qty);
                }
            }
            $order->update(['status' => $validated['status']]);
        });

        return back()->with('success', "Status pesanan #{$order->order_number} berhasil diperbarui menjadi " . ucfirst($validated['status']) . "!");
    }
}
