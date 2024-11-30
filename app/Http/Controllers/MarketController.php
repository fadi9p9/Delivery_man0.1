<?php

namespace App\Http\Controllers;

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
}
