<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products with pagination and optional search.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $products = $query->with(['images' => function ($query) {
            $query->take(1); 
        }])->paginate($request->get('per_page', 16));

        return response()->json($products);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::create($validatedData);

        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public'); 
                $product->images()->create(['path' => $path]);
            }
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
            'images' => $product->images,
        ], 201);
    }


    /**
     * Display the specified product.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::with('images')->findOrFail($id);

        return response()->json([
            'product' => $product,
            'images' => $product->images,
        ]);
    }


    /**
     * Update the specified product in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

    // new modified 

    /**
     * Allow users to rate a product.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validate the rating input
        $validatedData = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        // Update the product's rating
        $product->rate = ($product->rate * $product->rating_count + $validatedData['rating']) / ($product->rating_count + 1);
        $product->rating_count += 1;
        $product->save();

        return response()->json([
            'message' => 'Product rated successfully',
            'product' => $product,
        ]);
    }

    /**
     * Return top-rated products.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topRatedProducts(Request $request)
    {
        $limit = $request->get('limit', 12);
        $products = Product::orderBy('rate', 'desc')->take($limit)->get();

        return response()->json($products);
    }
}
