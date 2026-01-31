<?php

use App\Models\User;

it('shows attendance link for admins in the sidebar', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('employees.index'))
        ->assertSuccessful()
        ->assertSee(route('attendance.index'));
});

it('hides attendance link for cashiers in the sidebar', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($cashier)
        ->get(route('pos.index'))
        ->assertSuccessful()
        ->assertDontSee(route('attendance.index'));
});
