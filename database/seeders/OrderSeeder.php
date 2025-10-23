<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('No users or products found. Skipping order seeding.');

            return;
        }

        // Create 10 orders with random users and products
        Order::factory(10)->create([
            'user_id' => fn () => $users->random()->id,
        ])->each(function ($order) use ($products) {
            $detailsCount = rand(1, 5);
            $subtotal = 0;
            $tax = 0;
            $tippingPercentage = [0, 0.05, 0.1, 0.15][array_rand([0, 0.05, 0.1, 0.15])];
            $tipping = 0;
            $total = 0;

            for ($i = 0; $i < $detailsCount; $i++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $unitPrice = $product->price;
                $discount = fake()->randomFloat(2, 0, $unitPrice * $quantity * 0.2);
                $detailSubtotal = ($unitPrice * $quantity) - $discount;
                $subtotal += $detailSubtotal;

                OrderDetail::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'subtotal' => $detailSubtotal,
                ]);
            }

            $tax = $subtotal * 0.15;
            $tipping = $subtotal * $tippingPercentage;
            $total = $subtotal + $tax + $tipping;

            $order->update([
                'subtotal' => $subtotal,
                'tax_included' => false,
                'tax' => $tax,
                'tipping_percentage' => $tippingPercentage,
                'tipping' => $tipping,
                'discount_percentage' => 0,
                'discount' => 0,
                'total' => $total,
                'status' => 'completed',
            ]);
        });
    }
}
