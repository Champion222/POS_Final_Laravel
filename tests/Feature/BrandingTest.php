<?php

use App\Models\User;

it('shows the NEXPOX branding on the login page', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('NEXPOX')
        ->assertSee('https://i.postimg.cc/FHMsN52t/NEXPOS-Mart.png');
});

it('shows the NEXPOX branding on authenticated pages', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($cashier)
        ->get(route('pos.index'))
        ->assertSuccessful()
        ->assertSee('NEXPOX')
        ->assertSee('https://i.postimg.cc/FHMsN52t/NEXPOS-Mart.png');
});
