<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'income',
            'category_id' => Category::factory()->state(['type' => 'income']),
            'amount' => fake()->numberBetween(10000, 5000000),
            'date' => fake()->date(),
            'description' => fake()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
