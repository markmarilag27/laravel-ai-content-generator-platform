<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LowCreditsException extends Exception
{
    /**
     * Set the default message and the 402 "Payment Required" code.
     */
    public function __construct(string $message = 'Insufficient credits in workspace.')
    {
        parent::__construct($message, 402);
    }

    /**
     * The "API Response" chapter:
     * Automatically turn this exception into a standard JSON error.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => 'INSUFFICIENT_CREDITS',
            'message' => $this->getMessage(),
            'meta' => [
                'help' => 'Upgrade your plan or wait for the monthly reset.',
                'docs' => 'https://api.docs.com/credits',
            ],
        ], 402);
    }
}
