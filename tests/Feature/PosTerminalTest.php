<?php

use App\Models\Product;
use App\Models\User;

it('shows the pos terminal for cashier roles', function () {
    $user = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($user)
        ->get('/pos')
        ->assertSuccessful()
        ->assertSee('POS Terminal')
        ->assertSee('Cash')
        ->assertSee('Cash Payment')
        ->assertSee('Thank you', false)
        ->assertSee('Clear Cart?')
        ->assertSee('Cart is empty.');
});

it('exposes barcode data for auto-adding scanned items', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    Product::factory()->create([
        'barcode' => 'KHQR-EXAMPLE',
        'sale_price' => 4.00,
        'qty' => 5,
    ]);

    $this->actingAs($user)
        ->get('/pos')
        ->assertSuccessful()
        ->assertSee('"khqr-example"', false)
        ->assertSee('"price":', false);
});
