<?php

use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('updates employee details and user password when provided', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $currentPosition = Position::factory()->cashier()->create();
    $employeeUser = User::factory()->create([
        'role' => 'cashier',
        'email' => 'staff@example.com',
    ]);
    $employee = Employee::factory()->create([
        'position_id' => $currentPosition->id,
        'user_id' => $employeeUser->id,
        'email' => 'staff@example.com',
        'start_date' => '2026-01-20',
        'phone' => '0123456789',
    ]);
    $newPosition = Position::factory()->stockManager()->create();

    $this->actingAs($admin)
        ->put(route('employees.update', $employee), [
            'name' => 'Updated Staff',
            'email' => 'updated.staff@example.com',
            'phone' => '099999999',
            'start_date' => '2026-01-25',
            'position_id' => $newPosition->id,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])
        ->assertRedirect(route('employees.show', $employee));

    $employee->refresh();
    $employeeUser->refresh();

    expect($employee->position_id)->toBe($newPosition->id);
    expect($employee->email)->toBe('updated.staff@example.com');
    expect($employeeUser->email)->toBe('updated.staff@example.com');
    expect(Hash::check('newpassword', $employeeUser->password))->toBeTrue();
});

it('removes login when position does not require access', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $loginPosition = Position::factory()->cashier()->create();
    $noAccessPosition = Position::factory()->employee()->create();
    $employeeUser = User::factory()->create([
        'role' => 'cashier',
        'email' => 'remove.login@example.com',
    ]);
    $employee = Employee::factory()->create([
        'position_id' => $loginPosition->id,
        'user_id' => $employeeUser->id,
        'email' => 'remove.login@example.com',
        'start_date' => '2026-01-10',
        'phone' => '011222333',
    ]);

    $this->actingAs($admin)
        ->put(route('employees.update', $employee), [
            'name' => $employee->name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'start_date' => $employee->start_date,
            'position_id' => $noAccessPosition->id,
        ])
        ->assertRedirect(route('employees.show', $employee));

    $employee->refresh();

    expect($employee->user_id)->toBeNull();
    $this->assertDatabaseMissing('users', ['id' => $employeeUser->id]);
});
