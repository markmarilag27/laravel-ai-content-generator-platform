<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\LowCreditsException;
use App\Models\CreditLedger;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreditService
{
    /**
     * Deduct credits with a "Lazy Allotment" check.
     */
    public function deduct(Workspace $workspace, int $points, string $type, ?string $description = null): CreditLedger
    {
        return DB::transaction(function () use ($workspace, $points, $type, $description) {

            // Ensure the monthly bucket has been filled (for capped plans)
            $this->ensureMonthlyAllotment($workspace);

            // If it's Enterprise, we just record the usage without checking balance.
            if ($workspace->plan->monthly_credits === -1) {
                return $this->recordEntry($workspace, -$points, $type, $description);
            }

            // For Free/Pro, check the balance with a row-level lock
            $currentBalance = (int) CreditLedger::where('workspace_id', $workspace->id)
                ->lockForUpdate()
                ->sum('amount');

            if ($currentBalance < $points) {
                throw new LowCreditsException('Insufficient credits for this action.');
            }

            return $this->recordEntry($workspace, -$points, $type, $description);
        });
    }

    /**
     * Lazy-provisioning: The first time they use the app each month,
     * we "deposit" their plan credits into the ledger.
     */
    protected function ensureMonthlyAllotment(Workspace $workspace): void
    {
        $monthlyLimit = $workspace->plan->monthly_credits;

        // Unlimited/Enterprise doesn't need a deposit "bucket"
        if ($monthlyLimit === -1) {
            return;
        }

        $alreadyProvisioned = CreditLedger::where('workspace_id', $workspace->id)
            ->where('type', 'monthly_allotment')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->exists();

        if (! $alreadyProvisioned) {
            $this->recordEntry(
                $workspace,
                $monthlyLimit,
                'monthly_allotment',
                "Monthly allotment for {$workspace->plan->name} plan"
            );
        }
    }

    /**
     * Internal helper to keep the story clean.
     */
    protected function recordEntry(Workspace $workspace, int $amount, string $type, ?string $description): CreditLedger
    {
        return CreditLedger::create([
            'public_id' => (string) Str::uuid(),
            'workspace_id' => $workspace->id,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
        ]);
    }

    public function getBalance(Workspace $workspace): int
    {
        // If Enterprise, return a high number or a representation of infinity
        if ($workspace->plan->monthly_credits === -1) {
            return 999999;
        }

        return (int) CreditLedger::where('workspace_id', $workspace->id)->sum('amount');
    }
}
