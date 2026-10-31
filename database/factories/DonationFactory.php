<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'donor_name' => fake()->name(),
            'is_anonymous' => false,
            'amount' => fake()->numberBetween(10000, 1000000),
            'purpose' => 'infaq_umum',
            'payment_method' => 'manual',
            'status' => 'pending',
            'payment_reference' => 'INF-'.Str::upper((string) Str::uuid()),
        ];
    }
}
