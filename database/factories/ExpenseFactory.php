<?php

namespace Database\Factories;

use App\Enums\MeasureUnitEnum;
use App\Models\Supply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supply_id' => Supply::inRandomOrder()->first()?->id ?? Supply::factory(),
            'cost' => $this->faker->randomFloat(2, 1, 500),
            'quantity' => $this->faker->randomFloat(2, 0.5, 50),
            'measure_unit' => $this->faker->randomElement(MeasureUnitEnum::cases()),
            'notes' => $this->faker->optional()->sentence(),
            'date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }
}
