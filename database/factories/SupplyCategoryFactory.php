<?php

namespace Database\Factories;

use App\Models\Supply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplyCategory>
 */
class SupplyCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function ($supplyCategory) {
            Supply::factory(rand(3, 8))->create(['supply_category_id' => $supplyCategory->id]);
        });
    }
}
