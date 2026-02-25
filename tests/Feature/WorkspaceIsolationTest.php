<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_postgres_rls_strictly_isolates_records_between_workspaces(): void
    {
        // Arrange: Create two separate environments
        $workspaceA = Workspace::factory()->create(['name' => 'Workspace A']);
        $workspaceB = Workspace::factory()->create(['name' => 'Workspace B']);

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
        $workspace = Workspace::factory()->create();
        $user = User::factory()->create(['workspace_id' => $workspace->id]);

        $this->setPostgresContext($user->id, $workspace->id);
        CreditLedger::factory()->count(5)->create(['workspace_id' => $workspace->id]);

        // Act: Reset to an empty context (simulating a request with no active workspace)
        $this->setPostgresContext();

        $results = CreditLedger::all();

        // Assert: The database actively blocks reading the rows at the system level
        $this->assertCount(0, $results, 'Empty context must return absolutely zero records due to RLS enforcement.');
    }
}
