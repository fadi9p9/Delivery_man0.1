<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subcategories = Subcategory::with('category')->paginate($request->get('per_page', 16));
        return response()->json($subcategories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'categoryId' => 'required|exists:categories,id',
        ]);

        $subcategory = Subcategory::create($validated);
        return response()->json(['message' => 'SubCategory created successfully', 'subcategory' => $subcategory], 201);
    }

    public function show($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        return response()->json($subcategory);
    }

    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'categoryId' => 'required|exists:categories,id',
        ]);

        $subcategory->update($validated);
        return response()->json(['message' => 'SubCategory updated successfully', 'subcategory' => $subcategory]);
    }

    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();
        return response()->json(['message' => 'SubCategory deleted successfully']);
    }

    public function subcategoriesTitles(Request $request)
    {
        $subcategories = Subcategory::select('id', 'name')->get();

        return response()->json($subcategories);
    }
}
