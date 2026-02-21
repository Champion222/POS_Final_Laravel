<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $promotions = Promotion::query()
            ->withCount('products')
            ->latest()
            ->get();

        $activeCount = Promotion::query()->active()->count();
        $scheduledCount = Promotion::query()
            ->where('is_active', true)
            ->whereDate('start_date', '>', now())
            ->count();
        $expiredCount = Promotion::query()
            ->whereDate('end_date', '<', now())
            ->count();
        $promotedProducts = Product::query()
            ->whereHas('promotions', fn ($query) => $query->active())
            ->count();

        return view('promotions.index', compact(
            'promotions',
            'activeCount',
            'scheduledCount',
            'expiredCount',
            'promotedProducts'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::query()
            ->select('id', 'name', 'sale_price', 'image', 'qty', 'barcode', 'category_id')
            ->with('category:id,name')
            ->orderBy('name')
            ->get();
        $categories = Category::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('promotions.create', compact('products', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PromotionRequest $request): RedirectResponse
    {
        $promotion = Promotion::create($this->promotionAttributes($request));
        $promotion->products()->sync($request->input('products', []));

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion): View
    {
        $promotion->load('products:id');
        $selectedProducts = $promotion->products->pluck('id')->all();
        $products = Product::query()
            ->select('id', 'name', 'sale_price', 'image', 'qty', 'barcode', 'category_id')
            ->with('category:id,name')
            ->orderBy('name')
            ->get();
        $categories = Category::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('promotions.edit', compact('promotion', 'products', 'categories', 'selectedProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        $promotion->update($this->promotionAttributes($request));
        $promotion->products()->sync($request->input('products', []));

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->products()->detach();
        $promotion->delete();

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Promotion deleted successfully.');
    }

    /**
     * @return array{name:string, discount_value:float, type:string, start_date:string, end_date:string, is_active:bool}
     */
    private function promotionAttributes(PromotionRequest $request): array
    {
        $data = $request->safe()->only([
            'name',
            'discount_value',
            'type',
            'start_date',
            'end_date',
        ]);

        return [
            'name' => (string) $data['name'],
            'discount_value' => (float) $data['discount_value'],
            'type' => (string) $data['type'],
            'start_date' => (string) $data['start_date'],
            'end_date' => (string) $data['end_date'],
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
