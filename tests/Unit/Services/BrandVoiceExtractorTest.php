<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\AIProvider;
use App\Services\BrandVoiceExtractor;
use Mockery\MockInterface;
use Tests\TestCase;

class BrandVoiceExtractorTest extends TestCase
{
    public function test_it_extracts_profile_and_tracks_token_usage(): void
    {
        $mockAi = $this->mock(AIProvider::class, function (MockInterface $mock) {
            $mock->shouldReceive('chat')
                ->once()
                ->andReturn([
                    'content' => [
                        'tone' => 'Professional',
                        'formality' => 8,
                        'patterns' => ['Bullet points'],
                        'persona' => 'Editor',
                    ],
                    'usage' => ['total_tokens' => 1250],
                ]);
        });

        $extractor = new BrandVoiceExtractor($mockAi);
        $result = $extractor->extract(['Sample 1...', 'Sample 2...', 'Sample 3...']);

        // Assert the linguistics are preserved via TOON
        $this->assertEquals('Professional', $result['profile']['tone']);

        // Assert the metadata is tracked
        $this->assertEquals(1250, $result['tokens']);
    }
}
