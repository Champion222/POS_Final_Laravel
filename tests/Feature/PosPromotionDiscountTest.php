<?php

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\User;

it('applies promotions during checkout', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    $product = Product::factory()->create([
        'sale_price' => 10.00,
        'qty' => 10,
    ]);

    $promotion = Promotion::factory()->create([
        'type' => 'percent',
        'discount_value' => 20,
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $promotion->products()->attach($product->id);

    $response = $this->actingAs($user)->postJson(route('pos.store'), [
        'cart_data' => [
            ['id' => $product->id, 'qty' => 2],
        ],
        'payment_type' => 'cash',
    ]);

    $response->assertSuccessful()->assertJson(['status' => 'success']);

    $sale = Sale::query()->latest()->first();
    expect((float) $sale->total_amount)->toBe(20.0);
    expect((float) $sale->discount)->toBe(4.0);
    expect((float) $sale->final_total)->toBe(16.0);

    $detail = $sale->details()->first();
    expect((float) $detail->price)->toBe(10.0);
    expect((float) $detail->discount)->toBe(2.0);
    expect((float) $detail->subtotal)->toBe(16.0);

    $product->refresh();
    expect($product->qty)->toBe(8);
});
