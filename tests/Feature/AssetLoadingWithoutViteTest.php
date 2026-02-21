<?php

use App\Models\User;

test('guest pages load tailwind and alpine from cdn', function () {
    $this->get('/register')
        ->assertSuccessful()
        ->assertSee('https://cdn.tailwindcss.com', false)
        ->assertSee('https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', false)
        ->assertDontSee('/build/assets', false);
});

test('authenticated pages load tailwind and alpine from cdn', function () {
    $user = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSuccessful()
        ->assertSee('https://cdn.tailwindcss.com', false)
        ->assertSee('https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', false)
        ->assertDontSee('/build/assets', false);
});
