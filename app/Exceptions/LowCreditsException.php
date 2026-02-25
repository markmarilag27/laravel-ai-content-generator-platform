<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class LowCreditsException extends Exception
{
    public function __construct(string $message = 'Insufficient credits in workspace.')
    {
        parent::__construct(402, $message);
    }
}
