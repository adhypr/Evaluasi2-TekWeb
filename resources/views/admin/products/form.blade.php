<x-admin-layout :title="isset($product) ? 'Edit Product' : 'Add New Product'">
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-sm shadow-xl w-full max-w-2xl overflow-hidden border border-gray-200">

            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-gray-900">
                    {{ isset($product) ? 'Edit Product' : 'Add New Product' }}
                </h3>
                <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-black transition-colors" title="Close">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>

            <form action="{{ isset($product) ? route('admin.products.update', $product->product_id) : route('admin.products.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="p-6 space-y-4 max-h-[85vh] overflow-y-auto">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                @if ($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>&bull; {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                    <input type="text" name="name" required
                           value="{{ old('name', $product->name ?? '') }}"
                           placeholder="e.g., Nissan Skyline GT-R R34"
                           class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                        <input type="text" name="brand" required
                               value="{{ old('brand', $product->brand ?? '') }}"
                               placeholder="e.g., Hot Wheels, Mini GT"
                               class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" required
                                class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white text-gray-700">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->category_id }}" {{ old('category_id', $product->category_id ?? '') == $cat->category_id ? 'selected' : '' }}>
                                    {{ $cat->category ?? $cat->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                        <select name="item_condition" 
                                class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white text-gray-700">
                            <option value="New" {{ old('item_condition', $product->item_condition ?? 'New') == 'New' ? 'selected' : '' }}>
                                New (Mint)
                            </option>
                            <option value="Used" {{ old('item_condition', $product->item_condition ?? '') == 'Used' ? 'selected' : '' }}>
                                Loose (Bekas)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scale</label>
                        <input type="text" name="scale" required
                               value="{{ old('scale', $product->scale ?? '1:64') }}"
                               placeholder="1:64"
                               class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price (Rp)</label>
                        <input type="number" name="price" required min="0" step="1000"
                               value="{{ old('price', $product->price ?? '') }}"
                               placeholder="150000"
                               class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                        <input type="number" name="stock" required min="0"
                               value="{{ old('stock', $product->stock ?? 10) }}"
                               class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                    <input type="file" name="img" accept="image/*"
                           class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white file:mr-4 file:py-1 file:px-3 file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    @if(isset($product) && $product->img)
                        <div class="mt-2 h-20 w-20 border border-gray-200 rounded-sm overflow-hidden flex items-center justify-center bg-gray-50">
                            <img src="{{ $product->img_url }}" alt="Preview" class="max-h-full max-w-full object-contain mix-blend-multiply">
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Product description..."
                              class="w-full border border-gray-300 p-2 rounded-sm text-sm focus:border-black outline-none bg-white">{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                    <a href="{{ route('admin.products.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-sm text-sm font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-black text-white rounded-sm text-sm font-medium hover:bg-gray-800 transition-colors">
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
