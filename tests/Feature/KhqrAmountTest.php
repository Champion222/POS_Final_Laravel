<?php

use App\Models\User;
use KHQR\BakongKHQR;

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

it('embeds the amount in the KHQR payload', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    $response = $this->actingAs($cashier)
        ->postJson(route('pos.generate_qr'), ['amount' => '2.00'])
        ->assertSuccessful();

    $qrString = $response->json('qr_string');
    expect($qrString)->toBeString()->not->toBeEmpty();

    $decoded = BakongKHQR::decode($qrString);
    expect($decoded->data['transactionAmount'] ?? null)->toBe('2')
        ->and($decoded->data['transactionCurrency'] ?? null)->toBe('840');
});
