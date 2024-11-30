<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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
            'title' => 'required|string|max:255',
            'img' => 'nullable|string|max:255',
        ]);

        $category = Category::create($validated);
        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'img' => 'nullable|string|max:255',
        ]);

        $category->update($validated);
        return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

    // new function
    /**
     * Get all markets selling products in a specific category.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markets($id)
    {
        $category = Category::with('subcategories.products.market')->findOrFail($id);

        $markets = $category->subcategories->flatMap(function ($subcategory) {
            return $subcategory->products->pluck('market');
        })->unique('id')->values();

        return response()->json($markets);
    }
}
