<?php

use App\Models\User;

it('shows the positions link in the sidebar for admins', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('employees.index'))
        ->assertSuccessful()
        ->assertSee('Positions')
        ->assertSee(route('positions.index'));
});

it('hides the positions link for non-admin users', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($cashier)
        ->get(route('pos.index'))
        ->assertSuccessful()
        ->assertDontSee(route('positions.index'));
});
