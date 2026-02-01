<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->jobTitle(),
            'base_salary' => fake()->randomFloat(2, 150, 800),
            'target_role' => 'employee',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'name' => 'Administrator',
            'target_role' => 'admin',
        ]);
    }

    public function cashier(): static
    {
        return $this->state(fn () => [
            'name' => 'Cashier',
            'target_role' => 'cashier',
        ]);
    }

    public function stockManager(): static
    {
        return $this->state(fn () => [
            'name' => 'Stock Manager',
            'target_role' => 'stock_manager',
        ]);
    }

    public function employee(): static
    {
        return $this->state(fn () => [
            'name' => 'Staff',
            'target_role' => 'employee',
        ]);
    }
}
