<?php

namespace Database\Seeders;

use App\Enums\PlanName;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $monthlyCredits = [
            PlanName::Free->value => 50,
            PlanName::Pro->value => 500,
            PlanName::Enterprise->value => -1,
        ];

        foreach (PlanName::values() as $planName) {
            Plan::factory()->create([
                'name' => $planName,
                'monthly_credits' => $monthlyCredits[$planName],
            ]);
        }
    }
}
