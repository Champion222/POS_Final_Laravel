<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

it('updates profile details with image, email, and password', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $payload = [
        'name' => 'Updated User',
        'email' => 'updated.user@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
        'image' => UploadedFile::fake()->createWithContent(
            'avatar.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4//8/AwAI/AL+Ff6A3AAAAABJRU5ErkJggg==')
        ),
    ];

    $response = $this->actingAs($user)->patch(route('profile.update'), $payload);

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('status', 'profile-updated');

    $user->refresh();

    expect($user->name)->toBe('Updated User');
    expect($user->email)->toBe('updated.user@example.com');
    expect(Hash::check('new-password', $user->password))->toBeTrue();
    expect($user->image)->not->toBeNull();

    Storage::disk('public')->assertExists($user->image);
});

it('prevents non-admin users from changing password through profile update', function (string $role) {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => $role,
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Cashier Profile',
        'email' => 'cashier.profile@example.com',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
        'image' => UploadedFile::fake()->createWithContent(
            'avatar.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4//8/AwAI/AL+Ff6A3AAAAABJRU5ErkJggg==')
        ),
    ]);

    $response->assertSessionHasErrors('password');

    $user->refresh();

    expect($user->name)->not->toBe('Cashier Profile');
    expect($user->email)->not->toBe('cashier.profile@example.com');
    expect(Hash::check('old-password', $user->password))->toBeTrue();
})->with(['cashier', 'stock_manager']);

it('allows non-admin users to update image, name, and email without password', function (string $role) {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => $role,
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Updated Non Admin',
        'email' => 'updated.non.admin@example.com',
        'image' => UploadedFile::fake()->createWithContent(
            'avatar.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4//8/AwAI/AL+Ff6A3AAAAABJRU5ErkJggg==')
        ),
    ]);

    $response
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('status', 'profile-updated')
        ->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Updated Non Admin');
    expect($user->email)->toBe('updated.non.admin@example.com');
    expect(Hash::check('old-password', $user->password))->toBeTrue();
    expect($user->image)->not->toBeNull();

    Storage::disk('public')->assertExists($user->image);
})->with(['cashier', 'stock_manager']);
