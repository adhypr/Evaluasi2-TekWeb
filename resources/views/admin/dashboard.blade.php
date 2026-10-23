<x-admin-layout :title="'Dashboard Overview'">
    <div class="space-y-6">
        <!-- Dashboard Title -->
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>

        <!-- 3 Stat Cards Grid (Identik 1:1 dengan StatCard.jsx & AdminDashboard.jsx) -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div class="bg-white p-6 rounded-sm shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Revenue</p>
                <p class="text-3xl font-black text-gray-900 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-sm shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Orders</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ number_format($totalOrders, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-sm shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Product Catalog</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $totalProducts }} Items</p>
            </div>
        </div>

        <!-- Recent Orders Card (Identik 1:1 dengan OrdersTable.jsx & DataTable.jsx) -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>

                <!-- Search Input -->
                <form action="{{ route('admin.dashboard') }}" method="GET" class="w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="s" value="{{ request('s') }}" placeholder="Search orders / customers..." 
                               class="w-full border border-gray-200 rounded-sm pl-9 pr-3 py-2 text-sm focus:border-black outline-none bg-white">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID / Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order Items</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Paid</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($orders as $order)
                            @php
                                $badgeStyle = match ($order->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };

                                $orderData = [
                                    'order_number' => $order->order_number ?? $order->id,
                                    'customer_name' => $order->customer_name ?? 'Guest',
                                    'email' => $order->email ?? '-',
                                    'phone' => $order->phone ?? '-',
                                    'address' => $order->address ?? '-',
                                    'courier' => $order->shipping_method ?? 'Standard Delivery',
                                    'payment' => $order->payment_method ?? 'Bank Transfer',
                                    'date' => $order->created_at ? $order->created_at->format('M d, Y, h:i A') : '-',
                                    'status' => $order->status,
                                    'subtotal' => number_format($order->subtotal, 0, ',', '.'),
                                    'shipping_cost' => number_format($order->shipping_cost, 0, ',', '.'),
                                    'total' => number_format($order->total, 0, ',', '.'),
                                    'update_url' => route('admin.orders.update-status', $order),
                                    'items' => $order->items->map(fn($item) => [
                                        'name' => $item->product_name,
                                        'qty' => $item->qty,
                                        'subtotal' => number_format($item->price * $item->qty, 0, ',', '.')
                                    ])
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-black">
                                        #{{ $order->order_number ?? $order->id }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $order->customer_name ?? 'Guest' }}</div>
                                    <div class="text-xs text-gray-500 uppercase tracking-wider">{{ $order->shipping_method ?? 'Standard' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-0.5">
                                        @foreach ($order->items as $item)
                                            <li class="truncate max-w-[240px]" title="{{ $item->product_name }}">
                                                {{ $item->qty }}x {{ $item->product_name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeStyle }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button type="button"
                                            onclick='openOrderModal(@json($orderData))'
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-semibold rounded-sm text-gray-700 bg-white hover:bg-gray-100 hover:text-black transition-colors shadow-xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">
                                    {{ request('s') ? 'No orders found matching your search.' : 'No incoming orders yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Order Detail Modal (1:1 Identik dengan React OrderDetailModal.jsx) -->
    <div id="orderDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" onclick="closeOrderModal()"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <!-- Modal Box -->
            <div class="relative w-full max-w-2xl bg-white rounded-sm shadow-2xl border border-gray-200 overflow-hidden transform transition-all z-10">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
                    <h3 class="text-lg font-bold text-gray-900" id="modalOrderTitle">Order Details</h3>
                    <button type="button" onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-700 focus:outline-none text-2xl font-light leading-none cursor-pointer">
                        &times;
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    <!-- 2-Column Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Information -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Customer Information</h4>
                            <div class="text-sm text-gray-600 space-y-2">
                                <p><span class="font-medium text-gray-900">Name:</span> <span id="modalCustomerName">-</span></p>
                                <p><span class="font-medium text-gray-900">Email:</span> <span id="modalCustomerEmail">-</span></p>
                                <p><span class="font-medium text-gray-900">Phone:</span> <span id="modalCustomerPhone">-</span></p>
                                <p><span class="font-medium text-gray-900">Address:</span> <span id="modalCustomerAddress">-</span></p>
                            </div>
                        </div>

                        <!-- Shipping & Payment Info -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Shipping & Payment Info</h4>
                            <div class="text-sm text-gray-600 space-y-2">
                                <p><span class="font-medium text-gray-900">Courier:</span> <span id="modalCourier">-</span></p>
                                <p><span class="font-medium text-gray-900">Payment:</span> <span id="modalPayment">-</span></p>
                                <p><span class="font-medium text-gray-900">Order Date:</span> <span id="modalOrderDate">-</span></p>
                                <div class="pt-1">
                                    <form id="modalStatusForm" method="POST" action="" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <span class="font-medium text-gray-900 text-sm">Status:</span>
                                        <select id="modalStatusSelect" name="status" onchange="this.form.submit()"
                                                class="text-xs font-semibold px-2.5 py-1.5 border border-gray-300 rounded bg-white outline-none cursor-pointer focus:border-black transition-colors">
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </form>
                                    <p id="cancelledWarning" class="text-[11px] text-red-600 font-medium mt-1.5 hidden">
                                        ⚠️ Pesanan dibatalkan (stok otomatis dikembalikan).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item Details Table -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Item Details</h4>
                        <div class="border border-gray-200 rounded-sm overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500">Product</th>
                                        <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500">Qty</th>
                                        <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="modalItemsTableBody" class="bg-white divide-y divide-gray-200 text-sm">
                                    <!-- Dynamic rows inserted by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Rows -->
                    <div class="flex justify-end pt-2">
                        <div class="w-full md:w-1/2 space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Product Subtotal</span>
                                <span id="modalSubtotal">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping Cost</span>
                                <span id="modalShipping">Rp 0</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg text-gray-900 pt-2 border-t border-gray-200">
                                <span>Total Paid</span>
                                <span id="modalTotal">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button type="button" onclick="closeOrderModal()" class="bg-black text-white px-6 py-2 rounded-sm text-sm font-semibold hover:bg-gray-800 transition-colors shadow-sm cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Modal Interaktif -->
    <script>
        function openOrderModal(data) {
            document.getElementById('modalOrderTitle').innerText = 'Order Details #' + data.order_number;
            document.getElementById('modalCustomerName').innerText = data.customer_name;
            document.getElementById('modalCustomerEmail').innerText = data.email;
            document.getElementById('modalCustomerPhone').innerText = data.phone;
            document.getElementById('modalCustomerAddress').innerText = data.address;
            document.getElementById('modalCourier').innerText = data.courier;
            document.getElementById('modalPayment').innerText = data.payment;
            document.getElementById('modalOrderDate').innerText = data.date;

            // Update action URL pada form status
            const form = document.getElementById('modalStatusForm');
            form.action = data.update_url;

            const select = document.getElementById('modalStatusSelect');
            select.value = data.status;

            const warning = document.getElementById('cancelledWarning');
            if (data.status === 'cancelled') {
                select.disabled = true;
                select.classList.add('bg-gray-100', 'cursor-not-allowed');
                warning.classList.remove('hidden');
            } else {
                select.disabled = false;
                select.classList.remove('bg-gray-100', 'cursor-not-allowed');
                warning.classList.add('hidden');
            }

            // Render daftar items
            const tbody = document.getElementById('modalItemsTableBody');
            tbody.innerHTML = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2.5 text-gray-900 font-medium">${item.name}</td>
                        <td class="px-4 py-2.5 text-center text-gray-600">${item.qty}</td>
                        <td class="px-4 py-2.5 text-right text-gray-900 font-medium">Rp ${item.subtotal}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Tidak ada item</td></tr>';
            }

            // Summary nominal
            document.getElementById('modalSubtotal').innerText = 'Rp ' + data.subtotal;
            document.getElementById('modalShipping').innerText = 'Rp ' + data.shipping_cost;
            document.getElementById('modalTotal').innerText = 'Rp ' + data.total;

            // Buka modal
            document.getElementById('orderDetailModal').classList.remove('hidden');
        }

        function closeOrderModal() {
            document.getElementById('orderDetailModal').classList.add('hidden');
        }

        // Tutup modal jika tombol ESC ditekan
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeOrderModal();
            }
        });
    </script>
</x-admin-layout>
