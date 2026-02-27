<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\AIProvider;
use App\Services\BrandVoiceExtractor;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class BrandVoiceExtractorTest extends TestCase
{
    public function test_it_sends_correct_prompts_to_ai_for_extraction(): void
    {
        // Arrange: We mock the AI so we don't spend real money
        $mockAi = $this->mock(AIProvider::class, function (MockInterface $mock) {
            $mock->shouldReceive('chat')
                ->once()
                ->with(
                    Mockery::on(fn ($prompt) => str_contains($prompt, 'linguistics expert')),
                    Mockery::on(fn ($samples) => str_contains($samples, 'Sample 1'))
                )
                ->andReturn([
                    'tone' => 'Professional',
                    'formality' => 8,
                    'patterns' => ['Bullet points', 'Oxford commas'],
                    'persona' => 'A seasoned editor',
                ]);
        });

        $extractor = new BrandVoiceExtractor($mockAi);

        // Act
        $samples = ['Sample 1: Hello world', 'Sample 2: Eloquent is nice', 'Sample 3: RLS is secure'];
        $result = $extractor->extract($samples);

        // Assert
        $this->assertEquals('Professional', $result['tone']);
        $this->assertEquals(8, $result['formality']);
    }
}
