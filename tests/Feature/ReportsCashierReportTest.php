<?php

use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Str;

it('allows a cashier to filter, sort, and export sales reports', function () {
    $cashier = User::factory()->create(['role' => 'cashier']);

    Sale::query()->create([
        'user_id' => $cashier->id,
        'customer_id' => null,
        'invoice_number' => 'INV-' . Str::upper(Str::random(8)),
        'total_amount' => 50,
        'discount' => 0,
        'tax' => 0,
        'final_total' => 50,
        'payment_type' => 'cash',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($cashier)
        ->get(route('reports.sales', ['sort' => 'final_total', 'direction' => 'asc']))
        ->assertSuccessful()
        ->assertSee(route('reports.my_export', ['sort' => 'final_total', 'direction' => 'asc']));

    $exportResponse = $this->actingAs($cashier)
        ->get(route('reports.my_export', ['filter' => 'month', 'sort' => 'final_total', 'direction' => 'asc']))
        ->assertSuccessful();

    $contentDisposition = $exportResponse->headers->get('content-disposition');

    expect($contentDisposition)->not->toBeNull();
    expect(str_contains($contentDisposition, '.pdf'))->toBeTrue();
});
