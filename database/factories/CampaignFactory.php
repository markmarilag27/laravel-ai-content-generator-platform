<?php

namespace Database\Factories;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
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
            'title' => fake()->sentence(4),
            'brief' => fake()->paragraph(),
            'status' => fake()->randomElement(CampaignStatus::values()),
        ];
    }
}
