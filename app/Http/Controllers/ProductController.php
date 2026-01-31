<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display the inventory list with dashboard stats.
     */
    public function index()
    {
        // 1. Fetch Products (Paginated)
        $products = Product::with('category')->latest()->paginate(10);

        // 2. Calculate Stats for the "Cool UI" Dashboard
        $totalProducts = Product::count();
        
        // Calculate total value based on Cost Price (Assets) or Sale Price (Potential Revenue)
        // Using Cost Price is standard for Inventory Value. Change to 'sale_price' if you prefer revenue.
        $totalValue = Product::sum(DB::raw('sale_price * qty')); 
        
        $lowStockCount = Product::where('qty', '<', 10)->count();

        return view('products.index', compact('products', 'totalProducts', 'totalValue', 'lowStockCount'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        // Ensure Supplier model exists, or remove if not used
        $suppliers = class_exists(Supplier::class) ? Supplier::all() : []; 
        
        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'barcode' => 'required|string|unique:products,barcode',
            'qty' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $data = $request->all();

        // Image Upload Logic
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'barcode' => 'required|string|unique:products,barcode,' . $product->id, // Ignore current ID
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image to save space
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        return redirect()->back()->with('success', 'Product deleted successfully!');
    }
}