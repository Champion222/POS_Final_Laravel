<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('login form uses same-host relative action', function () {
    $this->get('/login')
        ->assertSuccessful()
        ->assertSee('action="/login"', false);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('legacy cashier and stock manager default password is upgraded during login', function (string $role) {
    $user = User::factory()->create([
        'role' => $role,
        'password' => Hash::make('nexpos@123'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'genz@123',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
    expect(Hash::check('genz@123', $user->refresh()->password))->toBeTrue();
})->with(['cashier', 'stock_manager']);

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
