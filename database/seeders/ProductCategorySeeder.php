<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategory::factory()
            ->count(5)
            ->withSubcategories(3) // Create 3 subcategories for each category, each with 5 products
            ->create();
    }
}
