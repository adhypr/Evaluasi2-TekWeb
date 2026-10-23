<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductWebController extends Controller
{
    public function index(Request $request)
    {
        $title = "Daftar Produk Diecast";
        $categories = Category::all();

        $query = Product::with('category');

        if ($request->filled('s')) {
            $keyword = trim($request->input('s'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('brand', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        $products = $query->orderBy('product_id', 'desc')->paginate(6)->withQueryString();

        return view('admin.products.index', compact('title', 'products', 'categories'));
    }

    public function create()
    {
        $title = "Tambah Produk Diecast Baru";
        $categories = Category::all();

        return view('admin.products.form', compact('title', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'brand' => 'required|string|max:100',
            'scale' => 'required|string|max:20',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'item_condition' => 'required|in:New,Used',
            'rating' => 'nullable|numeric|between:0,5',
            'description' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('products', 'public');
            $validated['img'] = Storage::url($path);
        } else {
            $validated['img'] = 'https://images.unsplash.com/photo-1581235720704-06d3acfcb36f?w=600&auto=format&fit=crop&q=60';
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['rating'] = $validated['rating'] ?? 5.0;

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk diecast berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.products.edit', $id);
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $title = "Edit Produk: " . $product->name;
        $categories = Category::all();

        return view('admin.products.form', compact('title', 'product', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'brand' => 'required|string|max:100',
            'scale' => 'required|string|max:20',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'item_condition' => 'required|in:New,Used',
            'rating' => 'nullable|numeric|between:0,5',
            'description' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('img')) {
        
            if ($product->img && str_starts_with($product->img, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $product->img);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('img')->store('products', 'public');
            $validated['img'] = Storage::url($path);
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk diecast berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        if ($product->img && str_starts_with($product->img, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $product->img);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}
