<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\AIProvider;
use App\Exceptions\LowCreditsException;
use App\Models\BrandVoiceProfile;
use App\Models\Workspace;
use App\Services\BrandVoiceGenerator;
use App\Services\CreditService;
use Mockery;
use Tests\TestCase;

class BrandVoiceGeneratorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test that the generator accumulates tokens and returns the correct structure.
     */
    public function test_it_accumulates_tokens_and_returns_structured_result(): void
    {
        $mockAi = Mockery::mock(AIProvider::class);
        $mockCredits = Mockery::mock(CreditService::class);

        // Mock the credit check to allow generation
        $mockCredits->shouldReceive('getBalance')->once()->andReturn(100);

        // Scenario: 1 Fail, 1 Success. Total 4 AI calls.
        $mockAi->shouldReceive('chat')
            ->times(4)
            ->andReturn(
                // Call 1: Generation (Bad) - Note the nested 'content' array
                ['content' => ['content' => 'Low quality text'], 'usage' => ['total_tokens' => 300]],
                // Call 2: Quality Check (Fail)
                ['content' => ['match_score' => 50], 'usage' => ['total_tokens' => 200]],
                // Call 3: Generation (Good)
                ['content' => ['content' => 'High quality on-brand text'], 'usage' => ['total_tokens' => 250]],
                // Call 4: Quality Check (Pass)
                ['content' => ['match_score' => 95], 'usage' => ['total_tokens' => 150]]
            );

        $generator = new BrandVoiceGenerator($mockAi, $mockCredits);

        // Setup mock-like model instances without hitting the DB
        $profile = new BrandVoiceProfile([
            'profile' => ['persona' => 'Tech Writer', 'tone' => 'Educational', 'formality' => 7],
        ]);
        $workspace = new Workspace;

        $brief = ['topic' => 'Laravel 12', 'content_type' => 'Blog Post', 'word_count' => 100];

        // Act
        $result = $generator->generate($profile, $brief, $workspace);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('High quality on-brand text', $result['content']);
        $this->assertEquals(900, $result['tokens']); // 300 + 200 + 250 + 150
        $this->assertEquals(2, $result['attempts']);
        $this->assertEquals(95, $result['final_score']);
    }

    /**
     * Test that it stops and returns the best it has after max retries.
     */
    public function test_it_stops_after_max_retries(): void
    {
        $mockAi = Mockery::mock(AIProvider::class);
        $mockCredits = Mockery::mock(CreditService::class);

        $mockCredits->shouldReceive('getBalance')->once()->andReturn(100);

        // Max retries is 2, so 3 attempts total = 6 AI calls
        $mockAi->shouldReceive('chat')
            ->times(6)
            ->andReturn(
                ['content' => ['content' => 'Attempt 1'], 'usage' => ['total_tokens' => 100]],
                ['content' => ['match_score' => 10], 'usage' => ['total_tokens' => 50]],
                ['content' => ['content' => 'Attempt 2'], 'usage' => ['total_tokens' => 100]],
                ['content' => ['match_score' => 20], 'usage' => ['total_tokens' => 50]],
                ['content' => ['content' => 'Attempt 3'], 'usage' => ['total_tokens' => 100]],
                ['content' => ['match_score' => 30], 'usage' => ['total_tokens' => 50]]
            );

        $generator = new BrandVoiceGenerator($mockAi, $mockCredits);

        $profile = new BrandVoiceProfile(['profile' => ['persona' => 'Editor', 'tone' => 'Sharp', 'formality' => 10]]);
        $workspace = new Workspace;

        $result = $generator->generate($profile, ['topic' => 'Testing', 'content_type' => 'Article', 'word_count' => 50], $workspace);

        $this->assertEquals('Attempt 3', $result['content']);
        $this->assertEquals(3, $result['attempts']);
        $this->assertEquals(450, $result['tokens']); // (100+50) * 3
        $this->assertEquals(30, $result['final_score']);
    }

    /**
     * Test that it halts immediately if the workspace has no credits.
     */
    public function test_it_throws_exception_if_insufficient_credits(): void
    {
        $mockAi = Mockery::mock(AIProvider::class);
        $mockCredits = Mockery::mock(CreditService::class);

        // Force a zero balance
        $mockCredits->shouldReceive('getBalance')->once()->andReturn(0);

        // Ensure AI is never actually called
        $mockAi->shouldNotReceive('chat');

        $generator = new BrandVoiceGenerator($mockAi, $mockCredits);

        $profile = new BrandVoiceProfile(['profile' => ['persona' => 'Writer']]);
        $workspace = new Workspace;

        // Assert the exception is thrown
        $this->expectException(LowCreditsException::class);

        // Act
        $generator->generate($profile, ['topic' => 'Test', 'content_type' => 'Blog', 'word_count' => 50], $workspace);
    }
}
