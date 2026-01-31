<?php

use App\Models\User;

it('shows the pos terminal for cashier roles', function () {
    $user = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($user)
        ->get('/pos')
        ->assertSuccessful()
        ->assertSee('POS Terminal')
        ->assertSee('Cash')
        ->assertSee('Cash Payment')
        ->assertSee('Cart is empty.');
});
