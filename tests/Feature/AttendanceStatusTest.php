<?php

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

it('marks a user as checked in when an open attendance exists', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    $checkinTime = Carbon::parse('2026-01-31 08:15:00');

    Attendance::create([
        'user_id' => $user->id,
        'date' => $checkinTime->toDateString(),
        'checkin_time' => $checkinTime,
        'checkout_time' => null,
        'status' => 'present',
    ]);

    $user->refresh();

    expect($user->isCheckedIn)->toBeTrue();
    expect($user->last_checkin_time?->equalTo($checkinTime))->toBeTrue();
});

it('shows last checkin time from the latest attendance when checked out', function () {
    $user = User::factory()->create(['role' => 'cashier']);
    $checkinTime = Carbon::parse('2026-01-30 09:00:00');

    Attendance::create([
        'user_id' => $user->id,
        'date' => $checkinTime->toDateString(),
        'checkin_time' => $checkinTime,
        'checkout_time' => Carbon::parse('2026-01-30 17:00:00'),
        'status' => 'present',
    ]);

    $user->refresh();

    expect($user->isCheckedIn)->toBeFalse();
    expect($user->last_checkin_time?->equalTo($checkinTime))->toBeTrue();
});
