<?php

namespace Database\Seeders;

use App\Enums\PlanName;
use App\Enums\UserRole;
use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use App\Traits\Database\InteractsWithPostgresRls;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use InteractsWithPostgresRls;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->setPostgresContext();

        $this->call([
            PlanSeeder::class,
        ]);

        $plan = Plan::query()->where('name', PlanName::Free)->firstOrFail();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'is_super_admin' => true,
        ]);

        $workspace = Workspace::factory()->create([
            'plan_id' => $plan->id,
            'name' => 'Test Workspace',
        ]);

        $user = User::factory()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $workspace->users()->attach($user->id, [
            'role' => UserRole::Admin,
            'workspace_id' => $workspace->id,
        ]);

        $this->command->info('Seeding complete. Workspace and User created with RLS bypassed.');
    }
}
