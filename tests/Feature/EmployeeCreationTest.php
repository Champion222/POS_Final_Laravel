<?php

use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an employee without a login for basic staff', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $position = Position::query()->create([
        'name' => 'Office Staff',
        'base_salary' => 300,
        'target_role' => 'employee',
    ]);

    $this->actingAs($admin)
        ->post(route('employees.store'), [
            'name' => 'Jane Staff',
            'email' => 'jane.staff@example.com',
            'phone' => '0123456789',
            'start_date' => '2026-01-15',
            'position_id' => $position->id,
        ])
        ->assertRedirect(route('employees.index'));

    $this->assertDatabaseHas('employees', [
        'email' => 'jane.staff@example.com',
        'user_id' => null,
    ]);

    $this->assertDatabaseMissing('users', [
        'email' => 'jane.staff@example.com',
    ]);
});

it('links an existing user account when the email already exists', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $existingUser = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'link@example.com',
        'role' => 'cashier',
    ]);
    $position = Position::query()->create([
        'name' => 'Cashier',
        'base_salary' => 200,
        'target_role' => 'cashier',
    ]);

    $this->actingAs($admin)
        ->post(route('employees.store'), [
            'name' => 'New Name',
            'email' => 'link@example.com',
            'phone' => '099999999',
            'start_date' => '2026-01-20',
            'position_id' => $position->id,
        ])
        ->assertRedirect(route('employees.index'));

    $employee = Employee::query()->where('email', 'link@example.com')->first();

    expect($employee)->not->toBeNull();
    expect($employee->user_id)->toBe($existingUser->id);

    $this->assertDatabaseHas('users', [
        'id' => $existingUser->id,
        'name' => 'New Name',
        'role' => 'cashier',
    ]);
});

it('assigns default password for cashier and stock manager accounts', function (string $role) {
    $admin = User::factory()->create(['role' => 'admin']);
    $position = Position::query()->create([
        'name' => ucfirst(str_replace('_', ' ', $role)),
        'base_salary' => 250,
        'target_role' => $role,
    ]);
    $email = "{$role}.default@example.com";

    $this->actingAs($admin)
        ->post(route('employees.store'), [
            'name' => ucfirst($role).' User',
            'email' => $email,
            'phone' => '011223344',
            'start_date' => '2026-01-16',
            'position_id' => $position->id,
        ])
        ->assertRedirect(route('employees.index'));

    $createdUser = User::query()->where('email', $email)->first();

    expect($createdUser)->not->toBeNull();
    expect($createdUser?->role)->toBe($role);
    expect(Hash::check('genz@123', (string) $createdUser?->password))->toBeTrue();

    $this->post('/logout');

    $this->post('/login', [
        'email' => $email,
        'password' => 'genz@123',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($createdUser);
})->with(['cashier', 'stock_manager']);
