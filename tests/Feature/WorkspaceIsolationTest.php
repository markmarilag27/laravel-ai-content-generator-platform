<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::query()->firstOrFail();
    }

    public function test_postgres_rls_strictly_isolates_records_between_workspaces(): void
    {
        // Arrange: Create two separate environments
        $workspaceA = Workspace::factory()->create(['name' => 'Workspace A', 'plan_id' => $this->plan->id]);
        $workspaceB = Workspace::factory()->create(['name' => 'Workspace B', 'plan_id' => $this->plan->id]);

        $userA = User::factory()->create(['workspace_id' => $workspaceA->id]);
        $userB = User::factory()->create(['workspace_id' => $workspaceB->id]);

        // Seed Workspace A
        $this->setPostgresContext($userA->id, $workspaceA->id);
        CreditLedger::factory()->count(3)->create(['workspace_id' => $workspaceA->id]);

        // Seed Workspace B
        $this->setPostgresContext($userB->id, $workspaceB->id);
        CreditLedger::factory()->count(2)->create(['workspace_id' => $workspaceB->id]);

        // Act & Assert for Workspace A
        $this->setPostgresContext($userA->id, $workspaceA->id);
        $resultsA = CreditLedger::all();

        $this->assertCount(3, $resultsA, 'User A should only see 3 records out of the 5 in the database.');
        $this->assertTrue(
            $resultsA->every(fn (CreditLedger $ledger): bool => $ledger->workspace_id === $workspaceA->id),
            'A record belonging to Workspace B leaked into Workspace A.'
        );

        // Act & Assert for Workspace B
        $this->setPostgresContext($userB->id, $workspaceB->id);
        $resultsB = CreditLedger::all();

        $this->assertCount(2, $resultsB, 'User B should only see 2 records.');
        $this->assertTrue(
            $resultsB->every(fn (CreditLedger $ledger): bool => $ledger->workspace_id === $workspaceB->id),
            'A record belonging to Workspace A leaked into Workspace B.'
        );
    }

    public function test_empty_postgres_context_returns_absolute_zero_records(): void
    {
        // Arrange
        $workspace = Workspace::factory()->create(['plan_id' => $this->plan->id]);
        $user = User::factory()->create(['workspace_id' => $workspace->id]);

        $this->setPostgresContext($user->id, $workspace->id);
        CreditLedger::factory()->count(5)->create(['workspace_id' => $workspace->id]);

        // Act: Reset to an empty context (simulating a request with no active workspace)
        $this->setPostgresContext();

        $results = CreditLedger::all();

        // Assert: The database actively blocks reading the rows at the system level
        $this->assertCount(0, $results, 'Empty context must return absolutely zero records due to RLS enforcement.');
    }

    public function test_super_admins_can_bypass_rls_and_view_all_data(): void
    {
        // Arrange: Create two workspaces with data
        $workspaceA = Workspace::factory()->create(['plan_id' => $this->plan->id]);
        $workspaceB = Workspace::factory()->create(['plan_id' => $this->plan->id]);

        // Add 3 records to A, 2 records to B
        $this->setPostgresContext(null, $workspaceA->id);
        CreditLedger::factory()->count(3)->create(['workspace_id' => $workspaceA->id]);

        $this->setPostgresContext(null, $workspaceB->id);
        CreditLedger::factory()->count(2)->create(['workspace_id' => $workspaceB->id]);

        // Reset to empty/system context so we can create a "global" user
        $this->setPostgresContext();

        // Act: Set context as a Super Admin (workspace ID doesn't matter here)
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $this->setPostgresContext($superAdmin->id, null, isSuperAdmin: true);

        $allResults = CreditLedger::all();
        $visibleWorkspaceIds = $allResults->pluck('workspace_id')->toArray();

        // Assert: Proves Super Admin bypassed RLS and can see other tenants' data
        $this->assertContains(
            $workspaceA->id,
            $visibleWorkspaceIds,
            "Super Admin should be able to see Workspace A's ledger."
        );

        $this->assertContains(
            $workspaceB->id,
            $visibleWorkspaceIds,
            "Super Admin should be able to see Workspace B's ledger."
        );

        // We expect AT LEAST 5 (plus any automatic ones the factory made)
        $this->assertGreaterThanOrEqual(5, $allResults->count());
    }
}
