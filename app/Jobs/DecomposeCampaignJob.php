<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\CampaignStatus;
use App\Jobs\Middleware\TenantScopeJobMiddleware;
use App\Models\Campaign;
use App\Models\CampaignItem;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DecomposeCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public int $workspaceId,
        public int $campaignId
    ) {
        $this->afterCommit = true;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new TenantScopeJobMiddleware];
    }

    public function handle(): void
    {
        DB::transaction(function () {
            Log::info(json_encode(Campaign::query()->get()->toArray(), JSON_PRETTY_PRINT));
            $campaign = Campaign::query()->findOrFail($this->campaignId);

            try {
                $brief = $campaign->brief;
                $jobs = [];

                // Loop through requested content types and quantities
                foreach ($brief['quantities'] as $contentType => $quantity) {
                    for ($i = 0; $i < $quantity; $i++) {

                        // Create the database record first
                        $campaignItem = CampaignItem::create([
                            'campaign_id' => $campaign->id,
                            'workspace_id' => $campaign->workspace_id,
                            'content_type' => $contentType,
                            'topic' => $brief['topic'],
                            'word_count' => $brief['word_counts'][$contentType] ?? 150,
                            'status' => CampaignStatus::Pending->value,
                        ]);

                        // Queue up the worker job
                        $jobs[] = new GenerateContentJob($this->userId, $this->workspaceId, $campaignItem->id);
                    }
                }

                $batch = Bus::batch($jobs)
                    ->allowFailures()
                    ->name("Campaign - {$campaign->title}")
                    ->finally(function (Batch $batch) use ($campaign) {
                        $campaign->update([
                            'status' => CampaignStatus::Completed->value,
                        ]);
                    })
                    ->dispatch();

                $campaign->update([
                    'job_batch_id' => $batch->id,
                    'status' => CampaignStatus::Processing->value,
                ]);
            } catch (Exception $e) {
                Log::error("Failed to decompose campaign: {$e->getMessage()}");
                $campaign->update(['status' => CampaignStatus::Failed->value]);
            }
        });
    }
}
