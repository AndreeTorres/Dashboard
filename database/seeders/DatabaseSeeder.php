<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::count() > 0) {
            $this->command->info('Database already seeded. Skipping...');

            return;
        }

        $this->call([
            ShieldSeeder::class,
            UserSeeder::class,
        ]);

        if (! app()->isProduction()) {
            $this->call([
                ProductCategorySeeder::class,

                TableSeeder::class,

                OrderSeeder::class,

                SupplyCategorySeeder::class,
                ExpenseSeeder::class,
            ]);

            $this->defaultProductImage();
        }
    }

    private function defaultProductImage(): void
    {
        $defaultImage = base_path('database/seeders/assets/images/default.png');
        $uniqueName = 'default.png';
        $destinationDir = public_path('storage/products');
        if (! file_exists($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }
        $destinationPath = $destinationDir.'/'.$uniqueName;
        if (file_exists($defaultImage)) {
            copy($defaultImage, $destinationPath);
        }
    }
}
