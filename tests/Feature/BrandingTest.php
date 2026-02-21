<?php

use App\Models\User;

it('shows the GenZPOS branding on the login page', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('GenZPOS')
        ->assertSee('https://i.postimg.cc/fTtdBdZf/Chat-GPT-Image-Feb-7-2026-03-27-39-PM.png');
});

it('shows the GenZPOS branding on authenticated pages', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($cashier)
        ->get(route('pos.index'))
        ->assertSuccessful()
        ->assertSee('GenZPOS')
        ->assertSee('https://i.postimg.cc/fTtdBdZf/Chat-GPT-Image-Feb-7-2026-03-27-39-PM.png');
});
