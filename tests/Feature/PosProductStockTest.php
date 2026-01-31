<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

it('returns current product stock levels for authenticated staff', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    $category = Category::create(['name' => 'Snacks']);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Test Product',
        'barcode' => fake()->unique()->ean13(),
        'cost_price' => 1.25,
        'sale_price' => 2.50,
        'qty' => 15,
    ]);

    $this->actingAs($user)
        ->getJson(route('pos.products.stock'))
        ->assertSuccessful()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('stocks.'.$product->id, $product->qty);
});
