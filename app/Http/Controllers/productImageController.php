<?php

namespace App\Http\Controllers;

use App\Models\productImage;
use Illuminate\Http\Request;

class productImageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'productId' => 'required|exists:products,id', // تأكد من أن ID المنتج موجود في جدول المنتجات
            'images' => 'required|array', // التأكد من أن الصور هي مصفوفة
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // التأكد من أن كل عنصر في المصفوفة هو صورة
        ]);
    
        // إضافة كل صورة مرتبطة بالمنتج
        foreach ($request->file('images') as $image) {
            $path = $image->store('products/images', 'public'); // حفظ الصورة في التخزين
    
            // إنشاء سجل جديد في قاعدة البيانات لكل صورة
            ProductImage::create([
                'productId' => $validated['productId'], // ربط الصورة بالمنتج
                'url' => $path, // مسار الصورة المخزنة
            ]);
        }
    
        return response()->json([
            'message' => 'Images added successfully',
        ], 201);
    }


   

    

    

    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);

        // حذف الصورة من التخزين
        if ($image->url) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->url);
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

    
}

