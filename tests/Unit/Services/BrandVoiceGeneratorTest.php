<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\AIProvider;
use App\Models\BrandVoiceProfile;
use App\Services\BrandVoiceGenerator;
use Mockery;
use Tests\TestCase;

class BrandVoiceGeneratorTest extends TestCase
{
    public function test_it_retries_generation_if_quality_gate_fails(): void
    {
        $mockAi = Mockery::mock(AIProvider::class);

        // Mockery sequence: Each argument to andReturn is used for one call in the sequence.
        // We expect 4 calls:
        // 1. Generation (Bad)
        // 2. Quality Check (Fail)
        // 3. Generation (Good)
        // 4. Quality Check (Pass)
        $mockAi->shouldReceive('chat')
            ->times(4)
            ->andReturn(
                ['content' => 'Generic text'],            // Call 1
                ['match_score' => 50],                    // Call 2
                ['content' => 'High quality text'],       // Call 3
                ['match_score' => 95]                     // Call 4
            );

        $generator = new BrandVoiceGenerator($mockAi);

        // We use a real model instance here, but since it's a Unit test,
        // we avoid hitting the DB by just filling the attribute.
        $profile = new BrandVoiceProfile([
            'profile' => [
                'tone' => 'Witty',
                'persona' => 'Comedian',
                'formality' => 5
            ]
        ]);

        // Act
        $result = $generator->generate($profile, [
            'topic' => 'Laravel',
            'word_count' => 100
        ]);

        // Assert
        $this->assertEquals('High quality text', $result);
    }
}
