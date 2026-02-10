<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->with(['items.product', 'user'])
            ->latest()
            ->get();

        return response()->json(['orders' => $orders]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update(['status' => $request->validated()['status']]);

        $order->load(['items.product', 'user']);

        return response()->json(['order' => $order]);
    }
}
