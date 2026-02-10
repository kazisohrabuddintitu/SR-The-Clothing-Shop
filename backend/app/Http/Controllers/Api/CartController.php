<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductSize;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected function getCart(Request $request): Cart
    {
        return $request->user()->cart()->firstOrCreate();
    }

    public function show(Request $request)
    {
        $cart = $this->getCart($request)->load('items.product');

        return response()->json(['cart' => $cart]);
    }

    public function store(AddCartItemRequest $request)
    {
        $cart = $this->getCart($request);
        $data = $request->validated();

        $productSize = ProductSize::query()
            ->where('product_id', $data['product_id'])
            ->where('size', $data['size'])
            ->first();

        if (! $productSize) {
            return response()->json(['message' => 'Size not available.'], 422);
        }

        $item = $cart->items()->firstOrCreate(
            ['product_id' => $data['product_id'], 'size' => $data['size']],
            ['quantity' => 0]
        );

        $newQuantity = $item->quantity + $data['quantity'];

        if ($newQuantity > $productSize->stock) {
            return response()->json(['message' => 'Not enough stock for this size.'], 422);
        }

        $item->quantity = $newQuantity;
        $item->save();

        $item->load('product');

        return response()->json(['item' => $item], 201);
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        $cart = $this->getCart($request);

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $productSize = ProductSize::query()
            ->where('product_id', $cartItem->product_id)
            ->where('size', $cartItem->size)
            ->first();

        if (! $productSize) {
            return response()->json(['message' => 'Size not available.'], 422);
        }

        $data = $request->validated();

        if ($data['quantity'] > $productSize->stock) {
            return response()->json(['message' => 'Not enough stock for this size.'], 422);
        }

        $cartItem->update($data);
        $cartItem->load('product');

        return response()->json(['item' => $cartItem]);
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        $cart = $this->getCart($request);

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Removed']);
    }
}
