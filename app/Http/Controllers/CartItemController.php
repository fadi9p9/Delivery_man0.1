<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = CartItem::with('product')->paginate($request->get('per_page', 16));
        return response()->json($cartItems);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cartId' => 'required|exists:carts,id',
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::create($validated);
        return response()->json(['message' => 'Cart item added successfully', 'cartItem' => $cartItem], 201);
    }

    public function update(Request $request, $id)
    {
        $cartItem = CartItem::findOrFail($id);
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->update($validated);  
        return response()->json(['message' => 'Cart item updated successfully', 'cartItem' => $cartItem]);
    }

    public function destroy($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();
        return response()->json(['message' => 'Cart item deleted successfully']);
    }
}
