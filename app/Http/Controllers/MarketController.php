<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class MarketController extends Controller
{
    public function index(Request $request)
{
    $markets = Market::paginate($request->get('per_page', 16));

    // تعديل البيانات وإضافة مسار storage/ للصور
    $markets->getCollection()->transform(function ($market) {
        if (isset($market->img)) { // تحقق من وجود الصورة
            $market->img = asset('storage/' . $market->img); // تعديل مسار الصورة
        }
        return $market;
    });

    return response()->json($markets);
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // التأكد من نوع الصورة
        ]);
    
        if ($request->hasFile('img')) {
            // حفظ الصورة في مجلد التخزين
            $path = $request->file('img')->store('markets', 'public');
            $validated['img'] = $path; // حفظ المسار فقط في قاعدة البيانات
        }
    
        $market = Market::create($validated);
    
        return response()->json([
            'message' => 'Market created successfully',
            'market' => $market,
        ], 201);
    }
    
    public function updateMarket(Request $request, $id)
    {
        // العثور على السجل المطلوب
        $market = Market::findOrFail($id);
        $img = $market->img;
    
        // التحقق من البيانات
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // التعامل مع الصورة إذا كانت موجودة
        if ($request->hasFile('img')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($img) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($img);
            }
    
            // تخزين الصورة الجديدة
            $path = $request->file('img')->store('markets', 'public');
            $validated['img'] = $path;
        }
    
        // تحديث السجل
        $market->update($validated);
    
        // إرجاع استجابة JSON
        return response()->json([
            'message' => 'Market updated successfully',
            'market' => [
                'id' => $market->id,
                'title' => $market->title,
                'location' => $market->location,
                'description' => $market->description,
                'img' => $market->img ? asset('storage/' . $market->img) : null,
            ],
        ]);
    }
    
    

    
public function show($id)
{
    $market = Market::with('products')->findOrFail($id);

    // تعديل رابط الصورة
    $market->img = $market->img ? asset('storage/' . $market->img) : null;

    return response()->json($market);
}


public function destroy($id)
{
    $market = Market::findOrFail($id);

    // حذف الصورة من التخزين إذا وُجدت
    if ($market->img) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($market->img);
    }

    $market->delete();

    return response()->json(['message' => 'Market deleted successfully']);
}


    public function rateMarket(Request $request, $id)
{
    $market = Market::findOrFail($id);

    $validatedData = $request->validate([
        'rating' => 'required|numeric|min:1|max:5',
    ]);

    if ($market->rating_count == 0) {
        // مشان اول تصنيف
        $market->rating = $validatedData['rating'];
    } else {
  $market->rating = round(($market->rating * $market->rating_count + $validatedData['rating']) / ($market->rating_count + 1), 1);

    }
    $market->rating_count += 1;
    $market->save();

    return response()->json([
        'message' => 'Market rated successfully',
        'market' => $market,
    ]);
}

    public function MarketTopRate(Request $request) {
        $limit = $request->get('limit', 12); 
        $markets = Market::orderBy('rating', 'desc')->take($limit)->get();
        return response()->json([ 'market'=>$markets ]);
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
