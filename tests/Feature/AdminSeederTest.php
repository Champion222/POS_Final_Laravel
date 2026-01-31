<?php

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Support\Facades\Hash;

it('is idempotent and upgrades existing admin users without overwriting passwords', function () {
    $existing = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'lorndavit12@gmail.com',
        'password' => Hash::make('secret-pass'),
        'role' => 'cashier',
    ]);

    $this->seed(AdminSeeder::class);
    $this->seed(AdminSeeder::class);

    $updated = User::query()->where('email', 'lorndavit12@gmail.com')->first();

    expect($updated)->not->toBeNull();
    expect($updated->id)->toBe($existing->id);
    expect($updated->role)->toBe('admin');
    expect($updated->name)->toBe('Lorn David');
    expect(Hash::check('secret-pass', $updated->password))->toBeTrue();

    expect(User::query()->where('email', 'nxygzz@gmail.com')->count())->toBe(1);
});
