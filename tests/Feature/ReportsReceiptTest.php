<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Str;

it('shows receipt links on the sales report', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $cashier = User::factory()->create(['role' => 'cashier']);

    $sale = Sale::query()->create([
        'user_id' => $cashier->id,
        'customer_id' => null,
        'invoice_number' => 'INV-' . Str::upper(Str::random(8)),
        'total_amount' => 100,
        'discount' => 0,
        'tax' => 0,
        'final_total' => 100,
        'payment_type' => 'cash',
    ]);

    $this->actingAs($admin)
        ->get(route('reports.sales'))
        ->assertSuccessful()
        ->assertSee($sale->invoice_number)
        ->assertSee(route('sales.receipt', $sale));
});

it('shows accurate totals on the receipt', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $cashier = User::factory()->create(['role' => 'cashier']);

    $category = Category::query()->create(['name' => 'Snacks']);
    $supplier = Supplier::query()->create(['name' => 'Acme Supply']);
    $product = Product::query()->create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'name' => 'Potato Chips',
        'barcode' => Str::upper(Str::random(12)),
        'cost_price' => 1.25,
        'sale_price' => 2.50,
        'qty' => 10,
        'image' => null,
    ]);

    $sale = Sale::query()->create([
        'user_id' => $cashier->id,
        'customer_id' => null,
        'invoice_number' => 'INV-' . Str::upper(Str::random(8)),
        'total_amount' => 10,
        'discount' => 1,
        'tax' => 0.5,
        'final_total' => 9.5,
        'payment_type' => 'qr',
    ]);

    SaleDetail::query()->create([
        'sale_id' => $sale->id,
        'product_id' => $product->id,
        'qty' => 4,
        'price' => 2.50,
        'subtotal' => 10,
    ]);

    $this->actingAs($admin)
        ->get(route('sales.receipt', $sale))
        ->assertSuccessful()
        ->assertSee($product->name)
        ->assertSee('Subtotal')
        ->assertSee('$10.00')
        ->assertSee('Discount')
        ->assertSee('-$1.00')
        ->assertSee('Tax')
        ->assertSee('$0.50')
        ->assertSee('$9.50')
        ->assertSee('KHQR');
});
