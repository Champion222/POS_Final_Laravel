<?php

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;

it('shows only today attendance by default', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-31 10:00:00'));
    $admin = User::factory()->create(['role' => 'admin']);
    $todayUser = User::factory()->create(['name' => 'Today User', 'role' => 'cashier']);
    $yesterdayUser = User::factory()->create(['name' => 'Yesterday User', 'role' => 'cashier']);

    Attendance::factory()->for($todayUser)->create([
        'date' => now()->toDateString(),
        'checkin_time' => now()->setTime(9, 0),
        'status' => 'present',
    ]);
    Attendance::factory()->for($yesterdayUser)->create([
        'date' => now()->subDay()->toDateString(),
        'checkin_time' => now()->subDay()->setTime(9, 0),
        'status' => 'present',
    ]);

    expect(Attendance::count())->toBe(2);
    expect(Attendance::whereDate('date', today()->toDateString())->count())->toBe(1);

    $this->actingAs($admin)
        ->get(route('attendance.index'))
        ->assertSuccessful()
        ->assertSee('font-bold text-gray-800 text-sm">Today User', false)
        ->assertDontSee('font-bold text-gray-800 text-sm">Yesterday User', false);

    Carbon::setTestNow();
});

it('filters attendance by week and month ranges', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-31 10:00:00'));
    $admin = User::factory()->create(['role' => 'admin']);
    $weekUser = User::factory()->create(['name' => 'Week User', 'role' => 'cashier']);
    $monthUser = User::factory()->create(['name' => 'Month User', 'role' => 'cashier']);

    Attendance::factory()->for($weekUser)->create([
        'date' => now()->startOfWeek()->addDay()->toDateString(),
        'checkin_time' => now()->startOfWeek()->addDay()->setTime(9, 0),
        'status' => 'present',
    ]);
    Attendance::factory()->for($monthUser)->create([
        'date' => now()->startOfMonth()->addDay()->toDateString(),
        'checkin_time' => now()->startOfMonth()->addDay()->setTime(9, 0),
        'status' => 'present',
    ]);

    $this->actingAs($admin)
        ->get(route('attendance.index', ['range' => 'week']))
        ->assertSuccessful()
        ->assertSee('font-bold text-gray-800 text-sm">Week User', false)
        ->assertDontSee('font-bold text-gray-800 text-sm">Month User', false);

    $this->actingAs($admin)
        ->get(route('attendance.index', ['range' => 'month']))
        ->assertSuccessful()
        ->assertSee('font-bold text-gray-800 text-sm">Week User', false)
        ->assertSee('font-bold text-gray-800 text-sm">Month User', false);

    Carbon::setTestNow();
});

it('exports attendance for the selected range', function () {
    Carbon::setTestNow(Carbon::parse('2026-01-31 10:00:00'));
    $admin = User::factory()->create(['role' => 'admin']);
    $todayUser = User::factory()->create(['name' => 'Today Export', 'role' => 'cashier']);
    $oldUser = User::factory()->create(['name' => 'Old Export', 'role' => 'cashier']);

    Attendance::factory()->for($todayUser)->create([
        'date' => now()->toDateString(),
        'checkin_time' => now()->setTime(9, 0),
        'status' => 'present',
    ]);
    Attendance::factory()->for($oldUser)->create([
        'date' => now()->subMonth()->toDateString(),
        'checkin_time' => now()->subMonth()->setTime(9, 0),
        'status' => 'present',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('attendance.export', ['range' => 'today']))
        ->assertSuccessful();

    expect($response->headers->get('content-type'))->toContain('application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('attendance-report-today.pdf');

    Carbon::setTestNow();
});
