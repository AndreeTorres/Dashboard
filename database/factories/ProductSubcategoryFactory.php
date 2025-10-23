<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subcategory>
 */
class ProductSubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'product_category_id' => ProductCategory::factory()->create()->id,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function ($subcategory) {
            Product::factory(5)->create(['product_subcategory_id' => $subcategory->id]);
        });
    }
}
