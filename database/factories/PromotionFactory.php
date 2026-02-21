<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['percent', 'fixed']);
        $discountValue = $type === 'percent'
            ? fake()->numberBetween(5, 40)
            : fake()->randomFloat(2, 1, 25);

        $startDate = Carbon::instance(fake()->dateTimeBetween('-3 days', '+3 days'));
        $endDate = (clone $startDate)->addDays(fake()->numberBetween(1, 10));

        return [
            'name' => fake()->words(2, true).' Promo',
            'discount_value' => $discountValue,
            'type' => $type,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'is_active' => true,
        ];
    }
}
