<?php

use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;

it('creates a promotion with products', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $products = Product::factory()->count(2)->create();

    $payload = [
        'name' => 'Weekend Sale',
        'type' => 'percent',
        'discount_value' => 20,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
        'is_active' => true,
        'products' => $products->pluck('id')->all(),
    ];

    $response = $this->actingAs($user)->post(route('promotions.store'), $payload);

    $response->assertRedirect(route('promotions.index'));
    $promotion = Promotion::query()->first();

    expect($promotion)->not->toBeNull();
    expect($promotion->products()->count())->toBe(2);
});

it('validates percent discounts', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    $payload = [
        'name' => 'Over 100',
        'type' => 'percent',
        'discount_value' => 150,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'products' => [$product->id],
    ];

    $response = $this->actingAs($user)->post(route('promotions.store'), $payload);

    $response->assertSessionHasErrors(['discount_value']);
});

it('allows cashier to view promotions but not manage them', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);
    $product = Product::factory()->create();
    $promotion = Promotion::factory()->create();

    $this->actingAs($cashier)
        ->get(route('promotions.index'))
        ->assertSuccessful();

    $this->actingAs($cashier)
        ->get(route('promotions.create'))
        ->assertForbidden();

    $this->actingAs($cashier)
        ->post(route('promotions.store'), [
            'name' => 'Cashier Promo',
            'type' => 'percent',
            'discount_value' => 10,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'products' => [$product->id],
        ])
        ->assertForbidden();

    $this->actingAs($cashier)
        ->get(route('promotions.edit', $promotion))
        ->assertForbidden();

    $this->actingAs($cashier)
        ->put(route('promotions.update', $promotion), [
            'name' => 'Updated Promo',
            'type' => 'fixed',
            'discount_value' => 5,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'products' => [$product->id],
        ])
        ->assertForbidden();

    $this->actingAs($cashier)
        ->delete(route('promotions.destroy', $promotion))
        ->assertForbidden();
});
