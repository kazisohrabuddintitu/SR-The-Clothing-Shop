<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $orderId = $request->query('order_id');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $toDate = $to ? Carbon::parse($to)->endOfDay() : now()->endOfMonth();

        $ordersQuery = Order::query()
            ->with(['items.product', 'user'])
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($orderId !== null && $orderId !== '') {
            $ordersQuery->where('id', (int) $orderId);
        }

        $orders = $ordersQuery->latest()->get();

        return response()->json(['orders' => $orders]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update(['status' => $request->validated()['status']]);

        $order->load(['items.product', 'user']);

        return response()->json(['order' => $order]);
    }
}
