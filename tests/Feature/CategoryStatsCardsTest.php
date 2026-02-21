<?php

use App\Models\User;

it('renders compact summary cards on the categories page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('categories.index'))
        ->assertSuccessful()
        ->assertSee('Total Categories')
        ->assertSee('Products Listed')
        ->assertSee('System Healthy')
        ->assertSee('min-h-[132px]', false);
});
