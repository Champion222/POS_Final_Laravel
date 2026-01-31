<?php

use App\Models\Employee;
use App\Models\Position;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;

it('renders the employee report with totals', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $cashier = User::factory()->create(['role' => 'cashier']);
    $position = Position::query()->create([
        'name' => 'Cashier',
        'base_salary' => 200,
        'target_role' => 'cashier',
    ]);

    $employee = Employee::query()->create([
        'name' => 'Test Employee',
        'email' => 'employee@example.com',
        'phone' => '010000000',
        'start_date' => '2026-01-30',
        'position_id' => $position->id,
        'user_id' => $cashier->id,
    ]);

    Sale::query()->create([
        'user_id' => $cashier->id,
        'customer_id' => null,
        'invoice_number' => 'INV-TEST-001',
        'total_amount' => 12.5,
        'discount' => 0,
        'tax' => 0,
        'final_total' => 12.5,
        'payment_type' => 'cash',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);

    $this->actingAs($admin)
        ->get(route('employees.report', ['employee' => $employee->id, 'filter' => 'month']))
        ->assertSuccessful()
        ->assertSee('$12.50')
        ->assertSee('Transactions');
});
