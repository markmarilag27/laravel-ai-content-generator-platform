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
     * The Exchange Rate: How many internal units represent 1,000 tokens.
     * 1,000 units = 1 Credit = 1,000 Tokens.
     * This means 1 unit = 1 token.
     */
    protected int $unitsPerThousandTokens = 1000;

    /**
     * Deduct credits with a "Lazy Allotment" check.
     */
    public function deduct(
        Workspace $workspace,
        int $tokens,
        string $type,
        ?string $description = null,
        array $meta = []
    ): CreditLedger {
        return DB::transaction(function () use ($workspace, $tokens, $type, $description, $meta): CreditLedger {

            // Ensure the monthly bucket has been filled (for capped plans)
            $this->ensureMonthlyAllotment($workspace);

            $amountToDeduct = $this->calculateTokenCost($tokens);

            // If it's Enterprise, we just record the usage without checking balance.
            if ($workspace->plan->isUnlimited()) {
                return $this->recordEntry($workspace->id, -$amountToDeduct, $type, $description, $meta);
            }

            // For Free/Pro, check the balance with a row-level lock
            $currentBalance = $this->getBalance($workspace);

            if ($currentBalance < $amountToDeduct) {
                throw new LowCreditsException;
            }

            return $this->recordEntry($workspace->id, -$amountToDeduct, $type, $description, $meta);
        });
    }

    /**
     * Calculate the internal unit cost for a given token count.
     *
     * @param  int  $tokens  The raw token count from OpenAI
     * @return int The amount of 'milli-credits' to deduct
     */
    public function calculateTokenCost(int $tokens): int
    {
        return (int) ceil($tokens / $this->unitsPerThousandTokens);
    }

    /**
     * Lazy-provisioning: The first time they use the app each month,
     * we "deposit" their plan credits into the ledger.
     */
    public function ensureMonthlyAllotment(Workspace $workspace): void
    {
        $plan = $workspace->plan;
        $monthlyLimit = $plan->monthly_credits;

        if ($plan->isUnlimited()) {
            return;
        }

        $alreadyProvisioned = CreditLedger::query()
            ->where('type', 'monthly_allotment')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->exists();

        if (! $alreadyProvisioned) {
            $this->recordEntry(
                $workspace->id,
                $monthlyLimit,
                'monthly_allotment',
                "Monthly allotment for {$plan->name->value} plan"
            );
        }
    }

    /**
     * Internal helper to record ledger entries.
     * Now public so it can be used for non-deduction audit logs (like extraction).
     */
    public function recordEntry(
        int $workspaceId,
        int $amount,
        string $type,
        ?string $description,
        array $meta = []
    ): CreditLedger {
        return CreditLedger::query()->create([
            'workspace_id' => $workspaceId,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'metadata' => $meta,
        ]);
    }

    /**
     * Get the current balance for a workspace.
     */
    public function getBalance(Workspace $workspace): int
    {
        if ($workspace->plan->isUnlimited()) {
            return 999999;
        }

        Workspace::query()->whereKey($workspace->id)->lockForUpdate()->first();

        return (int) CreditLedger::query()->sum('amount');
    }
}
