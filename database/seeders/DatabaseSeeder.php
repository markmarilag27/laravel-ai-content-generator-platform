<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Workspace;
use App\Traits\Database\InteractsWithPostgresRls;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use InteractsWithPostgresRls, WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->setPostgresContext();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $workspace = Workspace::factory()->create([
            'name' => 'Test Workspace',
        ]);

        $workspace->users()->attach($user->id, [
            'role' => UserRole::Admin,
            'workspace_id' => $workspace->id,
        ]);

        $user->update(['workspace_id' => $workspace->id]);
    }
}
