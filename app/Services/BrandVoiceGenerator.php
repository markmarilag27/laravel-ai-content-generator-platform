<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AIProvider;
use App\Models\BrandVoiceProfile;

class BrandVoiceGenerator
{
    protected int $maxRetries = 2;

    public function __construct(protected AIProvider $ai) {}

    public function generate(BrandVoiceProfile $profile, array $brief): string
    {
        return $this->attemptGeneration($profile, $brief, 0);
    }

    protected function attemptGeneration(BrandVoiceProfile $brandVoiceProfile, array $brief, int $attempts): string
    {
        $profile = $brandVoiceProfile->profile;

        // Generate the content
        $content = $this->ai->chat(
            $this->getSystemPrompt($profile),
            "Topic: {$brief['topic']}. Word count: {$brief['word_count']}."
        )['content'];

        // Check the Quality Gate
        $quality = $this->ai->chat(
            "Compare this text to this profile: " . json_encode($profile),
            "Text: {$content}. Return JSON with 'match_score' (1-100)."
        );

        // Logic: Retry if the score is low and we haven't hit the limit
        if ($quality['match_score'] < 80 && $attempts < $this->maxRetries) {
            return $this->attemptGeneration($brandVoiceProfile, $brief, $attempts + 1);
        }

        return $content;
    }

    protected function getSystemPrompt(array $profile): string
    {
        return "Act as: {$profile['persona']}. Tone: {$profile['tone']}. Formality: {$profile['formality']}/10.";
    }
}
