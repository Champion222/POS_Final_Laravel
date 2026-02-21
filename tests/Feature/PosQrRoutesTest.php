<?php

use App\Models\User;

test('pos view includes khqr endpoints', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);
    $generate = json_encode(route('pos.generate_qr'));
    $check = json_encode(route('pos.check_qr'));
    $stock = json_encode(route('pos.products.stock'));
    $store = json_encode(route('pos.store'));

    $this->actingAs($cashier)
        ->get(route('pos.index'))
        ->assertOk()
        ->assertSee($generate, false)
        ->assertSee($check, false)
        ->assertSee($stock, false)
        ->assertSee($store, false);
});
