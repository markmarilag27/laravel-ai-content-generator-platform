<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AIProvider;
use MischaSigtermans\Toon\Facades\Toon;

class BrandVoiceExtractor
{
    public function __construct(protected AIProvider $ai) {}

    public function extract(array $samples): array
    {
        $systemPrompt = <<<PROMPT
        You are a linguistics expert. Analyze the provided text samples to create a structured Brand Voice Profile.
        Return ONLY a JSON object with these keys:
        - tone: (string) The emotional quality (e.g., "Professional yet witty")
        - formality: (integer 1-10) 1 is casual/slang, 10 is academic/legal
        - patterns: (array of strings) Recurring vocabulary or sentence structures
        - persona: (string) A 1-sentence description of the 'author'
        PROMPT;

        $userPrompt = "Analyze these samples: \n\n" . implode("\n---\n", $samples);

        $rawJsonResponse = $this->ai->chat($systemPrompt, $userPrompt);

        return Toon::decode(Toon::encode($rawJsonResponse));
    }
}
