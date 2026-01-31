<?php

use App\Models\User;

it('returns a rounded amount for KHQR generation', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($cashier)
        ->postJson(route('pos.generate_qr'), ['amount' => '10.555'])
        ->assertSuccessful()
        ->assertJson([
            'status' => 'success',
            'amount' => '10.56',
        ]);
});
