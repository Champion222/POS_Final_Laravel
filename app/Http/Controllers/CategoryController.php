<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Get Categories with the Count of Products inside them
        $categories = Category::withCount('products')->latest()->paginate(10);
        
        // Calculate total stats for the top cards
        $totalCategories = Category::count();
        $totalProducts = \App\Models\Product::count();

        return view('categories.index', compact('categories', 'totalCategories', 'totalProducts'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create($request->all());
        return back()->with('success', 'Category added successfully.');
    }

    public function destroy(Category $category)
    {
        if($category->products_count > 0){
             return back()->with('error', 'Cannot delete. This category contains products.');
        }
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}