<?php

use App\Models\Attendance;
use App\Models\User;

it('flashes a sound message when already checked in', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    Attendance::factory()->create([
        'user_id' => $user->id,
        'checkout_time' => null,
    ]);

    $message = 'You already checked in, '.$user->name.'. Thank you.';

    $this->actingAs($user)
        ->post(route('attendance.store'), [
            'type' => 'clock_in',
        ])
        ->assertRedirect()
        ->assertSessionHas('play_sound_text', $message)
        ->assertSessionHas('error', $message);
});

it('plays a sound message on successful check-in', function () {
    $user = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($user)
        ->post(route('attendance.store'), [
            'type' => 'clock_in',
        ])
        ->assertRedirect()
        ->assertSessionHas('play_sound_text', 'Welcome! You are now clocked in. Thank you.')
        ->assertSessionHas('success');

    $this->assertDatabaseHas('attendances', [
        'user_id' => $user->id,
        'checkout_time' => null,
    ]);
});

it('plays a sound message on successful clock-out', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    Attendance::factory()->create([
        'user_id' => $user->id,
        'checkout_time' => null,
    ]);

    $this->actingAs($user)
        ->post(route('attendance.store'), [
            'type' => 'clock_out',
        ])
        ->assertRedirect()
        ->assertSessionHas('play_sound_text', 'Shift ended successfully. Thank you.')
        ->assertSessionHas('success');
});
