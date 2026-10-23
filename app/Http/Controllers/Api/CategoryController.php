<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::withCount('products')->orderBy('category_id')->get()
        );
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        return new CategoryResource(Category::create($data));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return new CategoryResource($category->refresh());
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
