<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private const SORTS = [
        'id' => ['product_id', 'asc'],
        '-id' => ['product_id', 'desc'],
        'price' => ['price', 'asc'],
        '-price' => ['price', 'desc'],
        'name' => ['name', 'asc'],
        '-name' => ['name', 'desc'],
    ];
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($search = trim((string) $request->query('search'))) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%"));
        }
        if ($brand = $request->query('brand')) {
            $query->where('brand', $brand);
        }
        if ($category = $request->query('category')) {
            $query->where('category_id', $category);
        }

        [$column, $direction] = self::SORTS[$request->query('sort', 'id')] ?? self::SORTS['id'];
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);

        return ProductResource::collection(
            $query->orderBy($column, $direction)->paginate($perPage)->withQueryString()
        );
    }

    public function store(StoreProductRequest $request)
    {
        $data = $this->normalize($request->validated());
        $data = $this->resolveCategory($data);
        $data['slug'] = Product::uniqueSlug($data['name']);

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('products', 'public');
        }

        $product = Product::create($data);

        return new ProductResource($product->load('category'));
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $this->normalize($request->validated());
        $data = $this->resolveCategory($data, $product);

        if (isset($data['name']) && $data['name'] !== $product->name) {
            $data['slug'] = Product::uniqueSlug($data['name'], $product->product_id);
        }

        if ($request->hasFile('img')) {
            $this->deleteImage($product);
            $data['img'] = $request->file('img')->store('products', 'public');
        }

        $product->update($data);

        return new ProductResource($product->refresh()->load('category'));
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->deleteImage($product);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
    private function normalize(array $data): array
    {
        if (array_key_exists('condition', $data)) {
            $data['item_condition'] = $data['condition'];
            unset($data['condition']);
        }
        foreach (['scale', 'item_condition', 'rating'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === null) {
                unset($data[$key]);
            }
        }
        unset($data['img']);

        return $data;
    }

    private function resolveCategory(array $data, ?Product $product = null): array
    {
        if (! empty($data['category_id'])) {
            $data['category_name'] ??= Category::findOrFail($data['category_id'])->category;
        } elseif (! empty($data['category_name'])) {
            $data['category_id'] = ($product && $product->category_name === $data['category_name'])
                ? $product->category_id
                : Category::firstOrCreate(['category' => $data['category_name']])->category_id;
        }

        return $data;
    }

    private function deleteImage(Product $product): void
    {
        $path = $product->img;
        if (! $path) {
            return;
        }

        $shared = Product::where('img', $path)->where('product_id', '!=', $product->product_id)->exists();
        if (! $shared) {
            Storage::disk('public')->delete($path);
        }
    }
}
