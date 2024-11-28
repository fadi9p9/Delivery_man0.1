<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::with('items.product')->paginate($request->get('per_page', 16));
        return response()->json($carts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
        ]);

        $cart = Cart::create($validated);
        return response()->json(['message' => 'Cart created successfully', 'cart' => $cart], 201);
    }

    public function addItem(Request $request, $cartId)
    {
        $validated = $request->validate([
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::findOrFail($cartId);
        $item = new CartItem($validated);
        $cart->items()->save($item);

        return response()->json(['message' => 'Item added to cart', 'item' => $item]);
    }

    public function removeItem($cartId, $itemId)
    {
        $cart = Cart::findOrFail($cartId);
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();
        return response()->json(['message' => 'Cart deleted successfully']);
    }
}
