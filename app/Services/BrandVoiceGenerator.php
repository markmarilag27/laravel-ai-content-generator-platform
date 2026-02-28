<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AIProvider;
use App\Exceptions\LowCreditsException;
use App\Models\BrandVoiceProfile;
use App\Models\Workspace;

class BrandVoiceGenerator
{
    protected int $maxRetries = 2;

    public function __construct(protected AIProvider $ai, protected CreditService $credits) {}

    /**
     * Start the generation process.
     *
     * @return array{content: string, tokens: int, attempts: int}
     */
    public function generate(BrandVoiceProfile $profile, array $brief, Workspace $workspace): array
    {
        $currentBalance = $this->credits->getBalance($workspace);

        if ($currentBalance < 1) {
            throw new LowCreditsException;
        }

        // Start recursion with 0 attempts and 0 initial tokens
        return $this->attemptGeneration($profile, $brief, 0, 0);
    }

    protected function attemptGeneration(
        BrandVoiceProfile $brandVoiceProfile,
        array $brief,
        int $attempts,
        int $totalTokens
    ): array {
        $profileData = $brandVoiceProfile->profile;

        // Ask for JSON in the prompt
        $genResponse = $this->ai->chat(
            $this->getSystemPrompt($profileData),
            "Topic: {$brief['topic']}. Word count: {$brief['word_count']}. You MUST return the result as a JSON object with a single key named 'content'."
        );

        $content = $genResponse['content']['content'] ?? 'Generation failed.';
        $totalTokens += ($genResponse['usage']['total_tokens'] ?? 0);

        // The Quality Gate (This already had the word "JSON", so it was fine!)
        $qualityResponse = $this->ai->chat(
            'Compare this text to this profile: '.json_encode($profileData),
            "Text: {$content}. Return JSON with 'match_score' (1-100)."
        );

        // Decode the Quality Gate response
        $matchScore = $qualityResponse['content']['match_score'] ?? 0;
        $totalTokens += ($qualityResponse['usage']['total_tokens'] ?? 0);

        // Retry if the score is low and we haven't hit the limit
        if ($matchScore < 80 && $attempts < $this->maxRetries) {
            return $this->attemptGeneration($brandVoiceProfile, $brief, $attempts + 1, $totalTokens);
        }

        // Final result as an array
        return [
            'content' => $content,
            'tokens' => $totalTokens,
            'attempts' => $attempts + 1,
            'final_score' => $matchScore,
        ];
    }

    protected function getSystemPrompt(array $profile): string
    {
        return "Act as: {$profile['persona']}. Tone: {$profile['tone']}. Formality: {$profile['formality']}/10.";
    }
}
