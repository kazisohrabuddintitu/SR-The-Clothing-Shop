<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->latest()->get();

        return response()->json(['products' => $products]);
    }

    public function show(Product $product)
    {
        $product->load(['sizes', 'reviews.user']);

        $similarProducts = Product::query()
            ->where('id', '!=', $product->id)
            ->when($product->category, function ($query) use ($product) {
                $query->where('category', $product->category);
            })
            ->latest()
            ->take(4)
            ->get();

        return response()->json([
            'product' => $product,
            'similar_products' => $similarProducts,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $sizes = $data['sizes'] ?? [];
        unset($data['sizes']);

        $product = Product::create($data);

        if (! empty($sizes)) {
            $product->sizes()->createMany($sizes);
        }

        return response()->json(['product' => $product], 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $sizes = $data['sizes'] ?? null;
        unset($data['sizes']);

        $product->update($data);

        if (is_array($sizes)) {
            $product->sizes()->delete();
            if (! empty($sizes)) {
                $product->sizes()->createMany($sizes);
            }
        }

        return response()->json(['product' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([], 204);
    }
}
