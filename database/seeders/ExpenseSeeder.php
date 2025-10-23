<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Supply;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplies = Supply::all();

        if ($supplies->isEmpty()) {
            $this->command->info('No supplies found. Skipping order seeding.');

            return;
        }

        Expense::factory(30)->create();
    }
}
