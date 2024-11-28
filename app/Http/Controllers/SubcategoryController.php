<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subcategories = SubCategory::with('category')->paginate($request->get('per_page', 16));
        return response()->json($subcategories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'img' => 'nullable|string|max:255',
            'categoryId' => 'required|exists:categories,id',
        ]);

        $subcategory = SubCategory::create($validated);
        return response()->json(['message' => 'SubCategory created successfully', 'subcategory' => $subcategory], 201);
    }

    public function show($id)
    {
        $subcategory = SubCategory::with('category')->findOrFail($id);
        return response()->json($subcategory);
    }

    public function update(Request $request, $id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'img' => 'nullable|string|max:255',
            'categoryId' => 'required|exists:categories,id',
        ]);

        $subcategory->update($validated);
        return response()->json(['message' => 'SubCategory updated successfully', 'subcategory' => $subcategory]);
    }

    public function destroy($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->delete();
        return response()->json(['message' => 'SubCategory deleted successfully']);
    }
}
