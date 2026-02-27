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
        $response = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        return json_decode($response->choices[0]->message->content, true);
    }
}
