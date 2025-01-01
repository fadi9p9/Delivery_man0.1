<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::paginate($request->get('per_page', 16));
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('categories', 'public');
            $validated['img'] = $path;
        }

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'img' => $category->img ? asset('storage/' . $category->img) : null,
            ],
        ], 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        $category->img = $category->img ? asset('storage/' . $category->img) : null;

        return response()->json($category);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('img')) {
            if ($category->img) {
                Storage::disk('public')->delete($category->img);
            }
            $path = $request->file('img')->store('categories', 'public');
            $validated['img'] = $path;
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'img' => $category->img ? asset('storage/' . $category->img) : null,
            ],
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->img) {
            Storage::disk('public')->delete($category->img);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function markets($id, Request $request)
    {
        $category = Category::with('subcategories.products.market')->findOrFail($id);

        $markets = $category->subcategories->flatMap(function ($subcategory) {
            return $subcategory->products->pluck('market');
        })->unique('id')->values();

        if ($request->has('search') && $request->search != '') {
            $markets = $markets->filter(function ($market) use ($request) {
                return stripos($market->name, $request->search) !== false;
            });
        }

        $perPage = $request->get('per_page', 16);
        $marketsPaginated = $markets->forPage($request->get('page', 1), $perPage);

        return response()->json([
            'data' => $marketsPaginated,
            'current_page' => $request->get('page', 1),
            'per_page' => $perPage,
            'total' => $markets->count(),
        ]);
    }

    public function products($id)
    {
        $category = Category::with('subcategories.products')->findOrFail($id);
        $category->img = $category->img ? asset('storage/' . $category->img) : null;

        return response()->json($category);
    }
}
