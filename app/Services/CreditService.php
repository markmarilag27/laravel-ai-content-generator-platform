<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\LowCreditsException;
use App\Models\CreditLedger;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class CreditService
{
    /**
     * Deduct credits safely using a DB transaction and locking.
     */
    public function deduct(Workspace $workspace, int $points, string $type, ?string $description = null): CreditLedger
    {
        return DB::transaction(function () use ($workspace, $points, $type, $description) {
            // Lock the sum calculation to prevent concurrent double-spending
            $currentBalance = CreditLedger::where('workspace_id', $workspace->id)
                ->lockForUpdate() // Standard PGSQL row-level lock
                ->sum('amount');

            if ($currentBalance < $points) {
                throw new LowCreditsException;
            }

            // Record the deduction
            return CreditLedger::create([
                'workspace_id' => $workspace->id,
                'amount' => -$points,
                'type' => $type,
                'description' => $description,
            ]);
        });
    }

    /**
     * Get the current balance for a workspace.
     */
    public function getBalance(Workspace $workspace): int
    {
        return (int) CreditLedger::where('workspace_id', $workspace->id)->sum('amount');
    }
}
