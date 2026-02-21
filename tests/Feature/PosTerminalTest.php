<?php

use App\Models\Product;
use App\Models\User;

it('shows the pos terminal for cashier roles', function () {
    $user = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($user)
        ->get('/pos')
        ->assertSuccessful()
        ->assertSee('POS Terminal')
        ->assertSee('xl:grid-cols-5', false)
        ->assertSee('lg:grid-cols-[minmax(0,1fr)_22rem]', false)
        ->assertSee('Cash')
        ->assertSee('Cash Payment')
        ->assertSee('KHQR Payment')
        ->assertSee('Processing Payment...')
        ->assertSee("mode === 'processing'", false)
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
