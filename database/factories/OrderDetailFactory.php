<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $quantity = $this->faker->numberBetween(1, 3);
        $unitPrice = $product ? $product->price : 0;
        $discount = $this->faker->randomFloat(2, 0, $unitPrice * $quantity * 0.2); // hasta 20% de descuento
        $subtotal = ($unitPrice * $quantity) - $discount;

        return [
            'order_id' => Order::factory(),
            'product_id' => $product ? $product->id : null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'subtotal' => $subtotal,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
