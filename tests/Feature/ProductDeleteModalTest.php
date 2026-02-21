<?php

use App\Models\Product;
use App\Models\User;

it('renders the delete confirmation modal on the products page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Product::factory()->create(['name' => 'Mint Tea']);

    $this->actingAs($admin)
        ->get(route('products.index'))
        ->assertSuccessful()
        ->assertSee('Delete product?', false)
        ->assertSee('openDeleteModal', false)
        ->assertSee('deleteModalOpen', false)
        ->assertDontSee("confirm('Delete this product?')", false);
});
