<?php

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('allows admins to open activity history page with filters', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    ActivityLog::query()->create([
        'user_id' => $admin->id,
        'event' => 'updated',
        'description' => 'Updated Product',
        'subject_type' => Product::class,
        'subject_id' => 1,
        'subject_label' => 'Milk',
        'method' => 'PUT',
        'route_name' => 'products.update',
        'url' => 'http://localhost/products/1',
        'ip_address' => '127.0.0.1',
        'properties' => [
            'old' => ['qty' => 10],
            'new' => ['qty' => 12],
        ],
    ]);

    $this->actingAs($admin)
        ->get(route('activities.index', ['range' => 'week', 'per_page' => 25]))
        ->assertSuccessful()
        ->assertSee('Activity History')
        ->assertSee('This Week')
        ->assertSee('Updated Product');
});

it('forbids non-admin users from opening activity history', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)
        ->get(route('activities.index'))
        ->assertForbidden();
})->with(['cashier', 'stock_manager']);

it('shows activity history link only for admins in sidebar', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $cashier = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($admin)
        ->get(route('employees.index'))
        ->assertSuccessful()
        ->assertSee(route('activities.index'));

    $this->actingAs($cashier)
        ->get(route('pos.index'))
        ->assertSuccessful()
        ->assertDontSee(route('activities.index'));
});

it('logs product stock updates with state data', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create(['qty' => 8]);

    $payload = [
        'name' => 'Updated Product Name',
        'category_id' => $product->category_id,
        'cost_price' => $product->cost_price,
        'sale_price' => $product->sale_price,
        'barcode' => $product->barcode,
        'qty' => 20,
    ];

    $this->actingAs($admin)
        ->put(route('products.update', $product), $payload)
        ->assertRedirect(route('products.index'));

    $log = ActivityLog::query()
        ->where('user_id', $admin->id)
        ->where('subject_type', Product::class)
        ->where('subject_id', $product->id)
        ->latest()
        ->first();

    expect($log)->not->toBeNull();
    expect($log?->event)->toBe('stock_updated');
    expect((int) data_get($log?->properties, 'old.qty'))->toBe(8);
    expect((int) data_get($log?->properties, 'new.qty'))->toBe(20);
});

it('logs admin password updates for own account and hides sensitive values', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->from('/profile')
        ->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect('/profile')
        ->assertSessionHasNoErrors();

    $log = ActivityLog::query()
        ->where('user_id', $admin->id)
        ->where('subject_type', User::class)
        ->where('subject_id', $admin->id)
        ->where('event', 'password_updated')
        ->latest()
        ->first();

    expect($log)->not->toBeNull();
    expect(data_get($log?->properties, 'old.password'))->toBe('[hidden]');
    expect(data_get($log?->properties, 'new.password'))->toBe('[hidden]');
    expect(Hash::check('new-password', $admin->refresh()->password))->toBeTrue();
});

it('logs admin password updates for other roles in activity history', function (string $role) {
    $admin = User::factory()->create(['role' => 'admin']);
    $position = match ($role) {
        'cashier' => Position::factory()->cashier()->create(),
        'stock_manager' => Position::factory()->stockManager()->create(),
        default => Position::factory()->admin()->create(),
    };

    $user = User::factory()->create([
        'name' => ucfirst(str_replace('_', ' ', $role)).' User',
        'email' => "{$role}.user@example.com",
        'role' => $role,
    ]);

    $employee = Employee::factory()->create([
        'name' => $user->name,
        'email' => $user->email,
        'phone' => '0123456789',
        'start_date' => '2026-02-10',
        'position_id' => $position->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($admin)
        ->put(route('employees.update', $employee), [
            'name' => $employee->name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'start_date' => $employee->start_date,
            'position_id' => $position->id,
            'password' => 'changed-by-admin',
            'password_confirmation' => 'changed-by-admin',
        ])
        ->assertRedirect(route('employees.show', $employee));

    $log = ActivityLog::query()
        ->where('user_id', $admin->id)
        ->where('subject_type', User::class)
        ->where('subject_id', $user->id)
        ->where('event', 'password_updated')
        ->latest()
        ->first();

    expect($log)->not->toBeNull();
    expect($log?->description)->toBe('Updated user password');
    expect(data_get($log?->properties, 'old.password'))->toBe('[hidden]');
    expect(data_get($log?->properties, 'new.password'))->toBe('[hidden]');
    expect(Hash::check('changed-by-admin', $user->refresh()->password))->toBeTrue();
})->with(['cashier', 'stock_manager']);
