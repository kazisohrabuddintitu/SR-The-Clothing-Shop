<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Order;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product')
            ->latest()
            ->get();

        return response()->json(['orders' => $orders]);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $order->load('items.product');

        return response()->json(['order' => $order]);
    }

    public function store(StoreOrderRequest $request)
    {
        $user = $request->user();
        $cart = $user->cart()->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 422);
        }

        $data = $request->validated();

        foreach ($cart->items as $item) {
            $productSize = ProductSize::query()
                ->where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->first();

            if (! $productSize || $item->quantity > $productSize->stock) {
                return response()->json(['message' => 'Not enough stock for selected sizes.'], 422);
            }
        }

        try {
            $order = DB::transaction(function () use ($user, $cart, $data) {
            $total = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $order = $user->orders()->create([
                'status' => 'pending',
                'total' => $total,
                'address_line1' => $data['address_line1'],
                'address_line2' => $data['address_line2'] ?? null,
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'],
                'country' => $data['country'],
            ]);

            foreach ($cart->items as $item) {
                $productSize = ProductSize::query()
                    ->where('product_id', $item->product_id)
                    ->where('size', $item->size)
                    ->lockForUpdate()
                    ->first();

                if (! $productSize || $item->quantity > $productSize->stock) {
                    throw new \RuntimeException('Not enough stock for selected sizes.');
                }

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $productSize->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();

                return $order;
            });
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if ($order instanceof \Illuminate\Http\JsonResponse) {
            return $order;
        }

        $order->load('items.product');

        return response()->json(['order' => $order], 201);
    }
}
