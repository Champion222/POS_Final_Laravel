<?php

use Illuminate\Support\Facades\Schema;

it('creates attendances table columns used by the app', function () {
    expect(Schema::hasTable('attendances'))->toBeTrue();

    $requiredColumns = [
        'user_id',
        'date',
        'checkin_time',
        'checkout_time',
        'status',
    ];

    foreach ($requiredColumns as $column) {
        expect(Schema::hasColumn('attendances', $column))->toBeTrue();
    }
});
