<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index(Request $request)
    {
        $markets = Market::paginate($request->get('per_page', 16));
        return response()->json($markets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'userId'=>'required|exists:users,id',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            
        ]);

        $market = Market::create($validated);
        return response()->json(['message' => 'Market created successfully', 'market' => $market], 201);
    }

    public function show($id)
    {
        $market = Market::findOrFail($id);
        return response()->json($market);
    }

    public function update(Request $request, $id)
    {
        $market = Market::findOrFail($id);
        $validated = $request->validate([
            // 'userId'=>'required|exists:users,id',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $market->update($validated);
        return response()->json(['message' => 'Market updated successfully', 'market' => $market]);
    }

    public function destroy($id)
    {
        $market = Market::findOrFail($id);
        $market->delete();
        return response()->json(['message' => 'Market deleted successfully']);
    }

    public function rateMarket(Request $request, $id)
    {
        $market = Market::findOrFail($id);

        // Validate the rating input
        $validatedData = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        // Update the market's rating
        $market->rating = ($market->rate * $market->rating_count + $validatedData['rating']) / ($market->rating_count + 1);
        $market->rating_count += 1;
        $market->save();

        return response()->json([
            'message' => 'Market rated successfully',
            'market' => $market,
        ]);
    }

    /**
     * Return top-rated markets.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topRatedMarkets(Request $request)
    {
        $limit = $request->get('limit', 12); 
        $markets = Market::orderBy('rating', 'desc')->take($limit)->get();

        return response()->json($markets);
    }

    // new function 
    /**
     * Get all categories that a market sells products in with pagination and optional search.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories($id, Request $request)
    {
        $market = Market::with('products.subcategory.category')->findOrFail($id);

        // استخراج التصنيفات من المنتجات
        // $categories = $market->products->flatMap(function ($product) {
        //     return $product->subcategory->pluck('categoryId');
        // })->unique('id')->values();
        // return response()->json($categories);

        // ا��تخرا�� التصنيفات من المنتجات التي تمتلكها المتا��ر بمعرف المتجر
        // $categories = Category::whereHas('subcategories.products', function ($query) use ($id) {
        //     $query->where('market_id', $id);
        // })->get();

        // ا��تخرا�� التصنيفات من المنتجات التي تمتلكها المتا��ر با��تخدام ��ريقة eager loading
        // $categories = Category::with(['subcategories.products' => function ($query) use ($id) {
        //     $query->where('market_id', $id);
        // }])->get();

        $categories = Category::whereHas('subcategories.products', function ($query) use ($id) {
            $query->where('marketId', $id);
        })->get();

        // البحث
        if ($request->has('search') && $request->search != '') {
            $categories = $categories->filter(function ($category) use ($request) {
                return stripos($category->name, $request->search) !== false;
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 16);  
        $categoriesPaginated = $categories->forPage($request->get('page', 1), $perPage);

        return response()->json([
            'data' => $categoriesPaginated,
            'current_page' => $request->get('page', 1),
            'per_page' => $perPage,
            'total' => $categories->count(),
        ]);
    }
}
