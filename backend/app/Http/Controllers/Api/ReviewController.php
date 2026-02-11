<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Product $product)
    {
        $reviews = $product->reviews()->with('user')->latest()->get();

        return response()->json(['reviews' => $reviews]);
    }

    public function store(StoreReviewRequest $request, Product $product)
    {
        $hasPurchased = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
                $query->where('status', 'delivered');
            })
            ->exists();

        if (! $hasPurchased) {
            return response()->json([
                'message' => 'You can only review products you have purchased.',
            ], 403);
        }

        $review = $product->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'rating' => $request->validated()['rating'],
                'comment' => $request->validated()['comment'] ?? null,
            ]
        );

        $review->load('user');

        return response()->json(['review' => $review], 201);
    }
}
