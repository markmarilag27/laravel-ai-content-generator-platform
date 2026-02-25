<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CreditLedger>
 */
class CreditLedgerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_id' => Str::uuid(),
            'amount' => fake()->randomElement([500, 100, -50, -10]),
            'type' => fake()->randomElement(['top_up', 'generation', 'refund']),
            'description' => fake()->sentence(),
            'workspace_id' => null,
        ];
    }
}
