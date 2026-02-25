<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BrandVoiceProfile>
 */
class BrandVoiceProfileFactory extends Factory
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
            'name' => fake()->catchPhrase().' Voice',
            'profile' => [
                'tone' => fake()->randomElement(['Professional', 'Casual', 'Humorous', 'Authoritative']),
                'vocabulary' => [fake()->word(), fake()->word(), fake()->word()],
                'constraints' => ['No jargon', 'Keep sentences short'],
            ],
        ];
    }
}
