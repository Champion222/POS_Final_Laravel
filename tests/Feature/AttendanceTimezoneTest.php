<?php

use App\Models\User;

it('uses Asia/Bangkok timezone for attendance time display', function () {
    expect(config('app.timezone'))->toBe('Asia/Bangkok');

    $cashier = User::factory()->create(['role' => 'cashier']);

    $this->actingAs($cashier);

    $html = view('attendances.index', ['myHistory' => collect()])->render();

    expect($html)->toContain('Bangkok Time (GMT+7)');
});
