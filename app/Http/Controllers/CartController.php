<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::with('cartItems')->paginate($request->get('per_page', 16));
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
    public function show($id){
        $cart = Cart::with('cartItems')->findOrFail($id);
        return response()->json($cart);
    }

    public function addItem(Request $request, $cartId)
    {
        $validated = $request->validate([
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::findOrFail($cartId);
        $item = new CartItem($validated);
        $cart->cartItems()->save($item);

        return response()->json(['message' => 'Item added to cart', 'item' => $item]);
    }

    public function removeItem($cartId, $itemId)
    {
        $cart = Cart::findOrFail($cartId);
        $item = $cart->cartItems()->findOrFail($itemId);
        $item->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();
        return response()->json(['message' => 'Cart deleted successfully']);
    }




    public function decrementQuantitie(Request $request)
    {
        $request->validate([
            'cartId' => 'required|exists:carts,id',
        ]);
    
        $cartItems = CartItem::where('cartId', $request->cartId)->get();
    
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'No items found in the cart'], 404);
        }
    
        DB::beginTransaction(); // بدء معاملة قاعدة البيانات
    
        try {
            $orderLog = [];
    
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
    
                if ($product->totalQuantity < $cartItem->quantity) {
                    return response()->json([
                        'message' => "Insufficient quantity for product ID {$product->id}"
                    ], 400);
                }
    
                // تقليل الكمية من المنتج
                $product->totalQuantity -= $cartItem->quantity;
                $product->save();
    
                // حفظ نسخة من الكميات المنقوصة
                $orderLog[] = [
                    'cartId' => $cartItem->cartId,
                    'cartItemId' => $cartItem->id,
                    'productId' => $product->id,
                    'decremented_quantity' => $cartItem->quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'expires_at' => now()->addMinutes(15), // مدة التراجع: ساعة
                ];
    
                // حذف العنصر من السلة
                $cartItem->delete();
            }
    
            // حفظ السجل في جدول order_logs
            DB::table('order_logs')->insert($orderLog);
    
            DB::commit(); // تأكيد العملية
    
            return response()->json(['message' => 'Quantities decremented successfully']);
        } catch (\Exception $e) {
            DB::rollBack(); // التراجع عن العملية في حال وجود خطأ
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function cancelOrder(Request $request)
{
    $request->validate([
        'cartId' => 'required|exists:carts,id',
    ]);

    // جلب السجل المرتبط بالسلة والتحقق من صلاحية مدة التراجع
    $orderLog = DB::table('order_logs')
        ->where('cartId', $request->cartId)
        ->where('expires_at', '>', now()) // تحقق من انتهاء صلاحية التراجع
        ->get();

    // التحقق من السجل
    if ($orderLog->isEmpty()) {
        return response()->json([
            'message' => 'No order to cancel or time expired',
            'expires_at' => DB::table('order_logs')->where('cartId', $request->cartId)->value('expires_at'),
            'now' => now()
        ], 403);
    }

    DB::beginTransaction();

    try {
        foreach ($orderLog as $log) {
            $product = Product::find($log->productId);

            if ($product) {
                // إرجاع الكمية إلى المنتج
                $product->totalQuantity += $log->decremented_quantity;
                $product->save();
            }

            // استعادة العنصر إلى السلة
            CartItem::create([
                'id' => $log->cartItemId,
                'cartId' => $request->cartId,
                'productId' => $log->productId,
                'quantity' => $log->decremented_quantity,
            ]);
        }

        // حذف السجل من order_logs
        DB::table('order_logs')->whereIn('cartItemId', $orderLog->pluck('cartItemId'))->delete();

        DB::commit();

        return response()->json(['message' => 'Order cancelled successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
    }
}


// التابع اذا بدي ادخل الغراض بايدي بس عالاغلب لازم عدل المتغيرات لتتناسب مع الموجودين بالداتا بيز

//     public function decrementCartQuantities(Request $request){
//     $request->validate([
//         'cartId' => 'required|exists:carts,id',
//         'quantities' => 'required|array', 
//         'quantities.*.productId' => 'required|exists:products,id',
//         'quantities.*.quantity' => 'required|integer|min:1'
//     ]);

//     $cartItems = CartItem::where('cartId', $request->cartId)->get();

//     if ($cartItems->isEmpty()) {
//         return response()->json(['message' => 'No items found in the cart'], 404);
//     }

//     foreach ($request->quantities as $item) {
//         $cartItem = $cartItems->where('productId', $item['productId'])->first();

//         if (!$cartItem) {
//             return response()->json(['message' => "Product ID {$item['productId']} not found in the cart"], 404);
//         }

//         // رح يفحصلي اذا العدد بيكفي؟
//         $product = $cartItem->product;
//         if ($product->totalQuantity < $item['quantity']) {
//             return response()->json([
//                 'message' => "Insufficient quantity for product ID {$item['productId']}"
//             ], 400);
//         }

//         // رح يطرح الكمية المطلوبة من الكمية الموجودة
//         $product->totalQuantity -= $item['quantity'];
//         $product->save();

//         // رح يعدل الكمية بعد الطرح
//         $cartItem->quantity -= $item['quantity'];
//         if ($cartItem->quantity <= 0) {
//             $cartItem->delete(); // Remove item from cart if quantity becomes 0
//         } else {
//             $cartItem->save();
//         }
//     }

//     return response()->json(['message' => 'All quantities updated successfully']);
// }

}
