<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\CampaignStatus;
use App\Jobs\Middleware\TenantScopeJobMiddleware;
use App\Models\CampaignItem;
use App\Services\BrandVoiceGenerator;
use App\Services\CreditService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class GenerateContentJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    private CampaignItem $item;

    public function __construct(
        public int $userId,
        public int $workspaceId,
        public int $campaignItemId
    ) {}

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new TenantScopeJobMiddleware];
    }

    public function handle(BrandVoiceGenerator $generator, CreditService $credits): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        DB::transaction(function () use ($generator, $credits) {
            $this->item = CampaignItem::query()
                ->with(['campaign.brandVoiceProfile', 'campaign.workspace'])
                ->findOrFail($this->campaignItemId);

            $this->item->update([
                'status' => CampaignStatus::Processing->value,
                'retry_count' => $this->attempts() - 1,
            ]);

            $result = $generator->generate(
                $this->item->campaign->brandVoiceProfile,
                [
                    'topic' => $this->item->topic,
                    'word_count' => $this->item->word_count,
                    'content_type' => $this->item->content_type,
                ],
                $this->item->campaign->workspace
            );

            $unitCost = $credits->calculateTokenCost($result['tokens']);

            $credits->deduct(
                $this->item->campaign->workspace,
                $unitCost,
                'generation',
                "Generated {$this->item->content_type} for Campaign: {$this->item->campaign->title}",
                [
                    'campaign_item_id' => $this->item->public_id,
                    'tokens' => $result['tokens'],
                    'attempts' => $this->attempts(),
                ]
            );

            $this->item->update([
                'output' => $result,
                'tokens_used' => $result['tokens'],
                'status' => CampaignStatus::Completed->value,
            ]);
        });

        // event(new CampaignStatusUpdated($this->item->campaign));
    }

    /**
     * Handle a job failure. (Runs after the 3rd failed attempt)
     */
    public function failed(Throwable $exception): void
    {
        $this->item->update([
            'status' => CampaignStatus::Failed->value,
            'error_message' => $exception->getMessage(),
        ]);

        // Trigger websocket to show failure on frontend
        // event(new CampaignStatusUpdated($this->item->campaign));
    }
}
