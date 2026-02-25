<?php

namespace Database\Factories;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaignItem>
 */
class CampaignItemFactory extends Factory
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
            'content_type' => fake()->randomElement(['LinkedIn Post', 'Twitter Thread', 'Email Newsletter']),
            'output' => null,
            'status' => CampaignStatus::Pending,
            'retry_count' => 0,
        ];
    }
}
