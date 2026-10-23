<x-admin-layout :title="'Product Management'">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Product Management</h1>

            <div class="flex w-full sm:w-auto gap-2 flex-wrap sm:flex-nowrap">
                <form action="{{ route('admin.products.index') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-64">
                        <input type="text" name="s" value="{{ request('s') }}" placeholder="Search products..." 
                               class="w-full border border-gray-200 rounded-sm pl-9 pr-3 py-2 text-sm focus:border-black outline-none bg-white">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <select name="category_id" onchange="this.form.submit()" 
                            class="border border-gray-200 rounded-sm px-3 py-2 text-sm focus:border-black outline-none bg-white text-gray-700">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ request('category_id') == $cat->category_id ? 'selected' : '' }}>
                                {{ $cat->category ?? $cat->category_name }}
                            </option>
                        @endforeach
                    </select>

                    @if(request('s') || request('category_id'))
                        <a href="{{ route('admin.products.index') }}" class="px-2 py-2 text-xs border border-gray-200 rounded-sm text-gray-500 hover:text-black flex items-center justify-center bg-white" title="Reset">
                            ✕
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.products.create') }}" 
                   class="bg-black text-white px-4 py-2 rounded-sm text-sm font-semibold hover:bg-gray-800 transition-colors whitespace-nowrap flex items-center justify-center">
                    + Add Product
                </a>
            </div>
        </div>
        <div class="bg-white shadow-sm border border-gray-100 rounded-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category/Brand
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-sm overflow-hidden border border-gray-200 flex items-center justify-center">
                                            <img class="h-10 w-10 object-contain mix-blend-multiply" 
                                                 src="{{ $product->img_url }}" 
                                                 alt="{{ $product->name }}"
                                                 onerror="this.onerror=null; this.src='https://placehold.co/60x60?text=Car'">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 truncate max-w-xs" title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">Stock: {{ $product->stock }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $product->brand }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->scale }} &bull; {{ $product->category->category ?? $product->category->category_name ?? $product->category_name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end">
                                        <a href="{{ route('admin.products.edit', $product->product_id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this product?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 bg-white">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
