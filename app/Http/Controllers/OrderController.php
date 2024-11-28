<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['cart', 'customer', 'delivery'])->paginate($request->get('per_page', 16));
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cartId' => 'required|exists:carts,id',
            'orderLocation' => 'required|string|max:255',
            'customerId' => 'required|exists:users,id',
            'deliveryId' => 'nullable|exists:users,id',
        ]);

        $order = Order::create($validated);
        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }

    public function show($id)
    {
        $order = Order::with(['cart', 'customer', 'delivery'])->findOrFail($id);
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'orderLocation' => 'required|string|max:255',
            'deliveryId' => 'nullable|exists:users,id',
        ]);

        $order->update($validated);
        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
