<?php

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id,
            'identifier' => strtoupper(fake()->unique()->bothify('ORD-####-??')),
            'subtotal' => 0,
            'tax_included' => true,
            'tax' => 0,
            'tipping_percentage' => 0,
            'tipping' => 0,
            'discount_percentage' => 0,
            'discount' => 0,
            'total' => 0,
            'status' => OrderStatusEnum::PENDING,
            'order_type' => fake()->randomElement([OrderTypeEnum::DINE_IN, OrderTypeEnum::TAKEAWAY]),
        ];
    }
}
