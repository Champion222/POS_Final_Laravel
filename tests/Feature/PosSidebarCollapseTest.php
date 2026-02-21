<?php

use App\Models\User;

it('shows sidebar collapse toggle only on pos page', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($cashier)
        ->get(route('pos.index'))
        ->assertSuccessful()
        ->assertSee('data-sidebar-toggle', false)
        ->assertSee('isPosPage: true', false)
        ->assertSee('sidebarCollapsed: true', false);

    $this->actingAs($cashier)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('isPosPage: false', false)
        ->assertSee('sidebarCollapsed: false', false);
});
