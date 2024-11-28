<?php

namespace App\Http\Controllers;

use App\Models\productImage;
use Illuminate\Http\Request;

class productImageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'productId' => 'required|exists:products,id',
            'url' => 'required|string|max:255',
        ]);

        $image = productImage::create($validated);
        return response()->json(['message' => 'Image added successfully', 'image' => $image], 201);
    }

    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);
        $image->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }
}

