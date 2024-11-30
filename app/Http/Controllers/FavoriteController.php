<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request, $userId)
    {

        $favorites = Favorite::where('userId', $userId)->with('product')->paginate($request->get('per_page', 16));
        return response()->json($favorites);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
            'productId' => 'required|exists:products,id',
        ]);

        $favorite = Favorite::create($validated);
        return response()->json(['message' => 'Product added to favorites', 'favorite' => $favorite], 201);
    }

    public function destroy($id)
    {
        $favorite = Favorite::findOrFail($id);
        $favorite->delete();
        return response()->json(['message' => 'Favorite item removed successfully']);
    }
}
