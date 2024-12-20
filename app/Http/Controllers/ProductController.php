<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the products with pagination and optional search.
     */
    public function index(Request $request)
{
    $query = Product::query();

    // البحث في العنوان والوصف
    if ($request->has('search') && $request->search != '') {
        $query->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
    }

    // جلب المنتجات مع الصور
    $products = $query->with(['images'])->paginate($request->get('per_page', 16));

    // تعديل مسار الصور في الـ response
    $products->getCollection()->transform(function ($product) {
        $product->images = $product->images->map(function ($image) {
            $image->url = asset('storage/' . $image->url); // إضافة storage/ للمسار
            return $image;
        });
        return $product;
    });

    return response()->json($products);
}


    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'marketId' => 'required|exists:markets,id',
        'subcategoryId' => 'required|exists:subcategories,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'size' => 'nullable|string|max:50',
        'discount' => 'nullable|numeric|min:0|max:100',
        'totalQuantity' => 'required|integer|min:0',
        'rate' => 'nullable|numeric|min:0|max:5',
        // 'images' => 'nullable|array',
        // 'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $product = Product::create($validatedData);

    // إذا كانت هناك صور، قم باستخدام `ProductImageController` لإضافتها
    if ($request->has('images')) {
        $productImageController = new ProductImageController();
        $request->merge(['productId' => $product->id]); // إضافة معرف المنتج إلى الطلب
        $productImageController->store($request);
    }

    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product,
        'images' => $product->images, // سيتم تحميل الصور المرتبطة بالمنتج هنا
    ], 201);
}


    /**
     * Display the specified product.
     */
   public function show($id)
{
    $product = Product::with('images')->findOrFail($id);

    // تعديل الصور لتضمين المسار الكامل
    $images = $product->images->map(function ($image) {
        return [
            'id' => $image->id,
            'url' => asset('storage/' . $image->url), // تضمين storage/
        ];
    });

    return response()->json([
        'product' => $product,
        'images' => $images,
    ]);
}


    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::with('images')->findOrFail($id);

        $validatedData = $request->validate([
            'marketId' => 'nullable|exists:markets,id',
            'subcategoryId' => 'nullable|exists:subcategories,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:50',
            'discount' => 'nullable|numeric|min:0|max:100',
            'totalQuantity' => 'nullable|integer|min:0',
            'rate' => 'nullable|numeric|min:0|max:5',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product->update($validatedData);

        if ($request->has('images')) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['path' => $path]);
            }
        }

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
            'images' => $product->images,
        ]);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Rate a product.
     */
    public function rateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $product->rate = round( ($product->rate * $product->rating_count + $validatedData['rating']) / ($product->rating_count + 1),1);
        $product->rating_count += 1;
        $product->save();

        return response()->json([
            'message' => 'Product rated successfully',
            'product' => $product,
        ]);
    }

    /**
     * Get top-rated products.
     */
    public function productTopRate(Request $request)
    {
        $limit = $request->get('limit', 12);
        $products = Product::with('images')->orderBy('rate', 'desc')->take($limit)->get();

        return response()->json(['products' => $products]);
    }
}
