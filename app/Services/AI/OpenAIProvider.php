<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AIProvider;
use OpenAI\Client;

class OpenAIProvider implements AIProvider
{
    public function __construct(
        protected Client $client,
        protected string $model = 'gpt-4o'
    ) {}

    public function chat(string $systemPrompt, string $userPrompt): array
    {
        $apiResponse = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        // Return the exact structure the Extractor and Generator expect
        return [
            'content' => json_decode($apiResponse->choices[0]->message->content, true),
            'usage' => [
                'total_tokens' => $apiResponse->usage->totalTokens ?? 0,
            ],
        ];
    }
}
