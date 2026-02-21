<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

it('sends a telegram notification when a payment is stored', function () {
    Http::fake();

    config([
        'services.telegram.bot_token' => 'test-token',
        'services.telegram.chat_id' => '123456',
    ]);

    $cashier = User::factory()->create(['role' => 'cashier', 'name' => 'Alex Cashier']);

    $category = Category::query()->create(['name' => 'Beverages']);
    $supplier = Supplier::query()->create(['name' => 'Local Supplier']);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'name' => 'Cola',
        'barcode' => Str::upper(Str::random(12)),
        'cost_price' => 0.5,
        'sale_price' => 2.5,
        'qty' => 20,
        'image' => null,
    ]);

    $this->actingAs($cashier)
        ->postJson(route('pos.store'), [
            'cart_data' => [
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 2.5,
                    'qty' => 2,
                    'image' => null,
                ],
            ],
            'customer_id' => null,
            'payment_type' => 'cash',
        ])
        ->assertSuccessful()
        ->assertJson(['status' => 'success']);

    Http::assertSent(function ($request) {
        $body = $request->body();

        return $request->url() === 'https://api.telegram.org/bottest-token/sendPhoto'
            && str_contains($body, 'name="chat_id"')
            && str_contains($body, '123456')
            && str_contains($body, 'GenZPOS Receipt')
            && str_contains($body, 'Cashier:')
            && str_contains($body, 'Alex Cashier')
            && str_contains($body, 'Total:')
            && str_contains($body, '$5.00');
    });
});
