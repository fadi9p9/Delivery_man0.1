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
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);
    
        foreach ($request->file('images') as $image) {
            $path = $image->store('products/images', 'public'); 
    
            ProductImage::create([
                'productId' => $validated['productId'], 
                'url' => $path,
            ]);
        }
    
        return response()->json([
            'message' => 'Images added successfully',
        ], 201);
    }

    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);

        if ($image->url) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->url);
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    
}

