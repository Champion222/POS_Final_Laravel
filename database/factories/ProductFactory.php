<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'supplier_id' => null,
            'name' => fake()->unique()->words(2, true),
            'barcode' => fake()->unique()->ean13(),
            'cost_price' => fake()->randomFloat(2, 1, 50),
            'sale_price' => fake()->randomFloat(2, 1, 100),
            'qty' => fake()->numberBetween(1, 50),
            'image' => null,
        ];
    }
}
