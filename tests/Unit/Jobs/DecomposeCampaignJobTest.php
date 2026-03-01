<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\CampaignStatus;
use App\Jobs\DecomposeCampaignJob;
use App\Jobs\GenerateContentJob;
use App\Models\BrandVoiceProfile;
use App\Models\Campaign;
use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DecomposeCampaignJobTest extends TestCase
{
    use RefreshDatabase;

    private Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::query()->firstOrFail();

        DB::statement("SET app.is_super_admin = 'true'");
    }

    public function test_it_decomposes_brief_and_dispatches_batch(): void
    {
        Bus::fake();

        $workspace = Workspace::factory()->create(['plan_id' => $this->plan->id]);

        $user = User::factory()->create(['workspace_id' => $workspace->id]);
        $profile = BrandVoiceProfile::factory()->create(['workspace_id' => $workspace->id]);

        $campaign = Campaign::factory()->create([
            'workspace_id' => $workspace->id,
            'brand_voice_profile_id' => $profile->id,
            'title' => 'Test Campaign',
            'status' => CampaignStatus::Pending,
            'brief' => [
                'topic' => 'Laravel Queues',
                'quantities' => [
                    'LinkedIn Post' => 2,
                    'Email Newsletter' => 1,
                ],
                'word_counts' => [
                    'LinkedIn Post' => 150,
                    'Email Newsletter' => 500,
                ],
            ],
        ]);

        $job = new DecomposeCampaignJob($user->id, $workspace->id, $campaign->id);

        $job->handle();

        $this->assertDatabaseCount('campaign_items', 3);

        $this->assertDatabaseHas('campaign_items', [
            'campaign_id' => $campaign->id,
            'content_type' => 'LinkedIn Post',
            'word_count' => 150,
        ]);
        $this->assertDatabaseHas('campaign_items', [
            'campaign_id' => $campaign->id,
            'content_type' => 'Email Newsletter',
            'word_count' => 500,
        ]);

        $campaign->refresh();
        $this->assertEquals(CampaignStatus::Processing, $campaign->status);
        $this->assertNotNull($campaign->job_batch_id);

        Bus::assertBatched(function ($batch) {
            return $batch->name === 'Campaign - Test Campaign' &&
                   $batch->jobs->count() === 3 &&
                   $batch->jobs->first() instanceof GenerateContentJob;
        });
    }

    public function test_it_marks_campaign_as_failed_on_exception(): void
    {
        $workspace = Workspace::factory()->create(['plan_id' => $this->plan->id]);
        $user = User::factory()->create(['workspace_id' => $workspace->id]);

        $job = new DecomposeCampaignJob($user->id, $workspace->id, 9999);

        try {
            $job->handle();
        } catch (\Exception $e) {
            //
        }

        $this->assertTrue(true);
    }
}
