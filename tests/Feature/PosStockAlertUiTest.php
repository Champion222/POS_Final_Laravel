<?php

use App\Models\User;

it('shows the stock alert toast container on POS', function () {
    $user = User::factory()->create(['role' => 'cashier']);

    $response = $this->actingAs($user)->get(route('pos.index'));

    $response->assertSuccessful();
    $response->assertSee('Stock Alert');
    $response->assertSee('stockAlertOpen', false);
});
