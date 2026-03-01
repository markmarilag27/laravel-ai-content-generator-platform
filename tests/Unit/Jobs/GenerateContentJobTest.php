<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\CampaignStatus;
use App\Events\CampaignStatusUpdated;
use App\Jobs\GenerateContentJob;
use App\Models\BrandVoiceProfile;
use App\Models\Campaign;
use App\Models\CampaignItem;
use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use App\Services\BrandVoiceGenerator;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class GenerateContentJobTest extends TestCase
{
    use RefreshDatabase;

    private Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::query()->firstOrFail();

        DB::statement("SET app.is_super_admin = 'true'");
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_generates_content_deducts_credits_and_broadcasts(): void
    {
        Event::fake([CampaignStatusUpdated::class]);

        $workspace = Workspace::factory()->create(['plan_id' => $this->plan->id]);
        $user = User::factory()->create(['workspace_id' => $workspace->id]);
        $profile = BrandVoiceProfile::factory()->create(['workspace_id' => $workspace->id]);

        $campaign = Campaign::factory()->create([
            'workspace_id' => $workspace->id,
            'brand_voice_profile_id' => $profile->id,
            'status' => CampaignStatus::Processing,
        ]);

        $item = CampaignItem::factory()->create([
            'campaign_id' => $campaign->id,
            'workspace_id' => $workspace->id,
            'content_type' => 'LinkedIn Post',
            'topic' => 'AI Frameworks',
            'word_count' => 100,
            'status' => CampaignStatus::Pending,
        ]);

        $mockGenerator = Mockery::mock(BrandVoiceGenerator::class);
        $mockGenerator->shouldReceive('generate')
            ->once()
            ->andReturn([
                'content' => 'This is a mocked AI response.',
                'tokens' => 150,
                'attempts' => 1,
            ]);

        $mockCredits = Mockery::mock(CreditService::class);
        $mockCredits->shouldReceive('calculateTokenCost')->once()->with(150)->andReturn(150);
        $mockCredits->shouldReceive('deduct')->once();

        $job = new GenerateContentJob($user->id, $workspace->id, $item->id);
        $job->handle($mockGenerator, $mockCredits);

        $item->refresh();
        $this->assertEquals(CampaignStatus::Completed->value, $item->status->value ?? $item->status);
        $this->assertEquals(150, $item->tokens_used);

        $campaign->refresh();

        Event::assertDispatched(CampaignStatusUpdated::class, function ($event) use ($campaign) {
            return $event->campaignPublicId === $campaign->public_id;
        });
    }

    public function test_it_handles_failure_and_broadcasts(): void
    {
        Event::fake([CampaignStatusUpdated::class]);

        $workspace = Workspace::factory()->create(['plan_id' => $this->plan->id]);
        $user = User::factory()->create(['workspace_id' => $workspace->id]);
        $profile = BrandVoiceProfile::factory()->create(['workspace_id' => $workspace->id]);

        $campaign = Campaign::factory()->create([
            'workspace_id' => $workspace->id,
            'brand_voice_profile_id' => $profile->id,
            'status' => CampaignStatus::Processing,
        ]);

        $item = CampaignItem::factory()->create([
            'campaign_id' => $campaign->id,
            'workspace_id' => $workspace->id,
            'content_type' => 'LinkedIn Post',
            'topic' => 'AI Error Handling',
            'word_count' => 100,
            'status' => CampaignStatus::Processing,
        ]);

        $job = new GenerateContentJob($user->id, $workspace->id, $item->id);

        $reflection = new \ReflectionClass($job);
        $property = $reflection->getProperty('item');
        $property->setAccessible(true);
        $property->setValue($job, $item);

        $job->failed(new \Exception('OpenAI API Connection Timeout'));

        $item->refresh();
        $this->assertEquals(CampaignStatus::Failed->value, $item->status->value ?? $item->status);
        $this->assertEquals('OpenAI API Connection Timeout', $item->error_message);

        $campaign->refresh();

        Event::assertDispatched(CampaignStatusUpdated::class, function ($event) use ($campaign) {
            return $event->campaignPublicId === $campaign->public_id;
        });
    }
}
