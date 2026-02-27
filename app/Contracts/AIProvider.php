<?php

namespace App\Contracts;

interface AIProvider
{
    /**
     * Send a system and user prompt and get a structured JSON response.
     */
    public function chat(string $systemPrompt, string $userPrompt): array;
}
