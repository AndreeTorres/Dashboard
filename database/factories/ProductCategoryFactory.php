<?php

namespace Database\Factories;

use App\Models\ProductSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
        ];
    }

    public function withSubcategories(int $count = 3): static
    {
        return $this->afterCreating(function ($category) use ($count) {
            ProductSubcategory::factory($count)->create(['product_category_id' => $category->id]);
        });
    }
}
